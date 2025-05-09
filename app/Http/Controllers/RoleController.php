<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        
        return Inertia::render('Settings/Roles/Index', [
            'roles' => $roles
        ]);
    }

    public function create()
    {
        $permissions = Permission::all();
        
        return Inertia::render('Settings/Roles/Create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array'
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }
        });

        return redirect()->route('settings.roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');
        
        return Inertia::render('Settings/Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        DB::transaction(function () use ($request, $role) {
            $role->update([
                'name' => $request->name
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }
        });

        return redirect()->route('settings.roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'owner') {
            return redirect()->route('settings.roles.index')
                ->with('error', 'No puedes eliminar el rol de propietario.');
        }

        $role->delete();

        return redirect()->route('settings.roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        
        return Inertia::render('Settings/Roles/Show', [
            'role' => $role
        ]);
    }
}