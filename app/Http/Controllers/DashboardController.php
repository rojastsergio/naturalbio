<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Clinic;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $stats = [];
        
        // Estadísticas generales para todos los roles
        $stats['total_users'] = User::count();
        $stats['total_clinics'] = Clinic::count();
        
        // Estadísticas específicas según el rol
        if ($user->hasRole('owner')) {
            // Estadísticas para propietarios
            $stats['active_clinics'] = Clinic::where('status', true)->count();
            $stats['inactive_clinics'] = Clinic::where('status', false)->count();
        }
        
        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}