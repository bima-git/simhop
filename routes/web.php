<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Coach\DashboardController as CoachDashboardController;
use App\Http\Controllers\Coach\ProjectController;
use App\Http\Controllers\Coach\TaskController as CoachTaskController;
use App\Http\Controllers\Coach\HandoverController;
use App\Http\Controllers\Coach\VideoReviewController;
use App\Http\Controllers\HandoverDownloadController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\TaskController as MahasiswaTaskController;
use App\Http\Controllers\Mahasiswa\ProjectController as MahasiswaProjectController;
use App\Http\Controllers\Mahasiswa\VideoSubmissionController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
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
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', AdminUserController::class)->except(['show', 'destroy']);
    Route::patch('users/{user}/toggle', [AdminUserController::class, 'toggle'])->name('users.toggle');
});

// ---------- Coach ----------
Route::middleware(['auth', 'role:coach'])->prefix('coach')->name('coach.')->group(function () {
    Route::get('/dashboard', [CoachDashboardController::class, 'index'])->name('dashboard');

    Route::resource('projects', ProjectController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('projects/{project}/members', [ProjectController::class, 'addMember'])->name('projects.members.store');
    Route::delete('projects/{project}/members/{user}', [ProjectController::class, 'removeMember'])->name('projects.members.destroy');

    Route::post('projects/{project}/tasks', [CoachTaskController::class, 'store'])->name('projects.tasks.store');
    Route::get('tasks/{task}/edit', [CoachTaskController::class, 'edit'])->name('tasks.edit');
    Route::match(['put', 'patch'], 'tasks/{task}', [CoachTaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{task}', [CoachTaskController::class, 'destroy'])->name('tasks.destroy');

    // Handover
    Route::get('projects/{project}/handover', [HandoverController::class, 'index'])->name('handover.index');
    Route::get('projects/{project}/handover/create', [HandoverController::class, 'create'])->name('handover.create');
    Route::post('projects/{project}/handover', [HandoverController::class, 'store'])->name('handover.store');
    Route::get('handover/{document}/edit', [HandoverController::class, 'edit'])->name('handover.edit');
    Route::put('handover/{document}', [HandoverController::class, 'update'])->name('handover.update');
    Route::delete('handover/{document}', [HandoverController::class, 'destroy'])->name('handover.destroy');

    // Review Video (T-05)
    Route::get('videos', [VideoReviewController::class, 'index'])->name('videos.index');
    Route::get('videos/{video}', [VideoReviewController::class, 'show'])->name('videos.show');
    Route::post('videos/{video}/approve', [VideoReviewController::class, 'approve'])->name('videos.approve');
    Route::post('videos/{video}/revise', [VideoReviewController::class, 'revise'])->name('videos.revise');
});

// ---------- Mahasiswa ----------
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('tasks', [MahasiswaTaskController::class, 'index'])->name('tasks.index');
    Route::get('tasks/{task}', [MahasiswaTaskController::class, 'show'])->name('tasks.show');
    Route::patch('tasks/{task}/progress', [MahasiswaTaskController::class, 'progress'])->name('tasks.progress');
    Route::get('projects/{project}', [MahasiswaProjectController::class, 'show'])->name('projects.show');

    // Video Submission (T-04)
    Route::post('tasks/{task}/video', [VideoSubmissionController::class, 'store'])->name('videos.store');
    Route::put('videos/{video}', [VideoSubmissionController::class, 'update'])->name('videos.update');
});

// ---------- Unduh handover (coach pemilik atau mahasiswa anggota) ----------
Route::middleware(['auth', 'role:coach,mahasiswa'])->group(function () {
    Route::get('handover/{document}/download', HandoverDownloadController::class)->name('handover.download');
});

require __DIR__.'/auth.php';