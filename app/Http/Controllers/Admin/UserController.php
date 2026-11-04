<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role');
        $status = $request->query('status');
        $search = $request->query('search');

        $query = User::query();

        if ($role && in_array($role, [User::ROLE_ADMIN, User::ROLE_COACH, User::ROLE_MAHASISWA], true)) {
            $query->where('role', $role);
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(15)->withQueryString();

        $counts = [
            'total' => User::count(),
            'admin' => User::where('role', User::ROLE_ADMIN)->count(),
            'coach' => User::where('role', User::ROLE_COACH)->count(),
            'mahasiswa' => User::where('role', User::ROLE_MAHASISWA)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'counts', 'role', 'status', 'search'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => $request->validated('role'),
            'nim' => $request->validated('role') === User::ROLE_MAHASISWA ? $request->validated('nim') : null,
            'is_active' => $request->boolean('is_active', true),
        ];

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // BR-06: Admin tidak boleh mengubah role akunnya sendiri
        if ($user->id === auth()->id() && $request->validated('role') !== $user->role) {
            abort(403, 'Admin tidak boleh mengubah role akunnya sendiri.');
        }

        $data = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
            'nim' => $request->validated('role') === User::ROLE_MAHASISWA ? $request->validated('nim') : null,
        ];

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        // BR-06: Admin tidak boleh menonaktifkan akun sendiri lewat form edit
        if ($request->has('is_active')) {
            $isActive = $request->boolean('is_active');
            if ($user->id === auth()->id() && ! $isActive) {
                abort(403, 'Admin tidak boleh menonaktifkan akunnya sendiri.');
            }
            $data['is_active'] = $isActive;
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Data pengguna berhasil diperbarui.');
    }

    public function toggle(User $user)
    {
        // BR-06: Admin tidak boleh menonaktifkan akunnya sendiri
        abort_if(
            $user->id === auth()->id(),
            403,
            'Admin tidak boleh menonaktifkan akunnya sendiri.'
        );

        // BR-06: Harus tersisa minimal satu Admin aktif
        if ($user->isAdmin() && $user->is_active) {
            $activeAdminCount = User::where('role', User::ROLE_ADMIN)->where('is_active', true)->count();
            if ($activeAdminCount <= 1) {
                return back()->with('error', 'Tidak dapat menonaktifkan akun. Harus tersisa minimal satu Admin aktif.');
            }
        }

        $user->update(['is_active' => ! $user->is_active]);

        $message = $user->is_active
            ? "Akun {$user->name} berhasil diaktifkan kembali."
            : "Akun {$user->name} berhasil dinonaktifkan.";

        return back()->with('status', $message);
    }
}
