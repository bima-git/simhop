<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', User::ROLE_ADMIN)->where('is_active', true)->count(),
            'coach' => User::where('role', User::ROLE_COACH)->where('is_active', true)->count(),
            'mahasiswa' => User::where('role', User::ROLE_MAHASISWA)->where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }
}
