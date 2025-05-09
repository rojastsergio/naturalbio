<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Index');
    }

    public function modules()
    {
        return Inertia::render('Settings/Modules');
    }

    public function forms()
    {
        return Inertia::render('Settings/Forms');
    }
}