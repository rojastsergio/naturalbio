<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'clinic_id' => $request->user()->clinic_id,
                    'roles' => $request->user() ? $request->user()->roles->pluck('name') : [],
                    'permissions' => $request->user() ? $request->user()->getAllPermissions()->pluck('name') : [],
                ] : null,
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
                'success' => fn () => $request->session()->get('success'),
            ],
            'appName' => config('app.name'),
            'can' => [
                'create_recommendations' => $request->user() ? $request->user()->can('create_recommendations') : false,
                'update_recommendations' => $request->user() ? $request->user()->can('update_recommendations') : false,
                'delete_recommendations' => $request->user() ? $request->user()->can('delete_recommendations') : false,

            'create_supplements' => $request->user() ? $request->user()->can('create supplements') : false,
            'update_supplements' => $request->user() ? $request->user()->can('update supplements') : false,
            'delete_supplements' => $request->user() ? $request->user()->can('delete supplements') : false,
        ],
        ]);
    }
}