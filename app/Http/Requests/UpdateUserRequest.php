<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $targetUser = $this->route('user');
        $targetId = $targetUser instanceof User ? $targetUser->id : $targetUser;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($targetId)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_COACH, User::ROLE_MAHASISWA])],
            'nim' => [
                Rule::requiredIf(fn () => $this->input('role') === User::ROLE_MAHASISWA),
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'nim')->ignore($targetId),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama pengguna wajib diisi.',
            'name.max' => 'Nama pengguna tidak boleh lebih dari 255 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'password.min' => 'Kata sandi minimal 8 karakter bila ingin diubah.',
            'role.required' => 'Peran (role) pengguna wajib dipilih.',
            'role.in' => 'Peran pengguna tidak valid.',
            'nim.required' => 'NIM wajib diisi untuk pengguna dengan role Mahasiswa.',
            'nim.unique' => 'NIM ini sudah digunakan oleh mahasiswa lain.',
            'nim.max' => 'NIM maksimal 20 karakter.',
        ];
    }
}
