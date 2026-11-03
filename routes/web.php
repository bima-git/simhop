<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Coach\ProjectController;

Route::get('/', function () {
    return view('welcome');
});

// Breeze mengalihkan ke route('dashboard') setelah login; di sini pengguna
// diarahkan sesuai role. Semua role dimasukkan agar cek akun aktif tetap jalan.
Route::get('/dashboard', function (Request $request) {
    return redirect()->route($request->user()->role.'.dashboard');
})->middleware(['auth', 'role:admin,coach,mahasiswa'])->name('dashboard');

Route::middleware(['auth', 'role:admin,coach,mahasiswa'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ---------- Admin ----------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // TODO: ganti closure dengan controller (manajemen pengguna)
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});

// ---------- Coach ----------
Route::middleware(['auth', 'role:coach'])->prefix('coach')->name('coach.')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::resource('projects', ProjectController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('projects/{project}/members', [ProjectController::class, 'addMember'])->name('projects.members.store');
    Route::delete('projects/{project}/members/{user}', [ProjectController::class, 'removeMember'])->name('projects.members.destroy');
});

// ---------- Mahasiswa ----------
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    // TODO: handover, tugas, kirim video
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});

require __DIR__.'/auth.php';