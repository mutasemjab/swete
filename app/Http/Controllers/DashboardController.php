<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $modules = config('modules');

        return view('dashboard.index', compact('modules'));
    }
}
