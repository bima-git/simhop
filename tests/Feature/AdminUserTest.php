<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_dashboard_and_users_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Admin');

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Manajemen Pengguna');
    }

    public function test_admin_can_create_new_user_for_each_role(): void
    {
        $admin = User::factory()->admin()->create();

        // 1. Buat Mahasiswa (dengan NIM)
        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Budi Mahasiswa',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MAHASISWA,
            'nim' => '2024009',
            'is_active' => true,
        ]);
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'role' => User::ROLE_MAHASISWA,
            'nim' => '2024009',
            'is_active' => true,
        ]);

        // Verifikasi dapat login
        $this->post(route('login'), [
            'email' => 'budi@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        // 2. Buat Coach
        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Pak Coach',
            'email' => 'coach_baru@example.com',
            'password' => 'password123',
            'role' => User::ROLE_COACH,
            'is_active' => true,
        ])->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'coach_baru@example.com',
            'role' => User::ROLE_COACH,
            'nim' => null,
        ]);

        // 3. Buat Admin Baru
        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Admin Dua',
            'email' => 'admin_baru@example.com',
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ])->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'admin_baru@example.com',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'terdaftar@example.com']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Duplikat User',
            'email' => 'terdaftar@example.com',
            'password' => 'password123',
            'role' => User::ROLE_COACH,
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_nim_is_required_for_mahasiswa_role(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Mahasiswa Tanpa NIM',
            'email' => 'nonim@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MAHASISWA,
            'nim' => '',
        ]);

        $response->assertSessionHasErrors(['nim']);
        $this->assertDatabaseMissing('users', ['email' => 'nonim@example.com']);
    }

    public function test_duplicate_nim_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->mahasiswa()->create(['email' => 'mhs1@example.com', 'nim' => '2024001']);

        // Coba buat mahasiswa baru dengan email berbeda tapi NIM sama
        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Mahasiswa Kloning',
            'email' => 'mhs2@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MAHASISWA,
            'nim' => '2024001',
        ]);

        $response->assertSessionHasErrors(['nim']);
        $this->assertDatabaseMissing('users', ['email' => 'mhs2@example.com']);
    }

    public function test_admin_can_edit_and_update_user(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->mahasiswa()->create([
            'name' => 'Nama Lama',
            'email' => 'lama@example.com',
            'nim' => '2024100',
        ]);

        // Buka form edit
        $this->actingAs($admin)
            ->get(route('admin.users.edit', $student))
            ->assertOk()
            ->assertSee('Nama Lama');

        // Update data termasuk reset kata sandi
        $response = $this->actingAs($admin)->put(route('admin.users.update', $student), [
            'name' => 'Nama Baru',
            'email' => 'baru@example.com',
            'password' => 'newpassword123',
            'role' => User::ROLE_MAHASISWA,
            'nim' => '2024101',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $student->refresh();
        $this->assertSame('Nama Baru', $student->name);
        $this->assertSame('baru@example.com', $student->email);
        $this->assertSame('2024101', $student->nim);
        $this->assertTrue(Hash::check('newpassword123', $student->password));
    }

    public function test_admin_can_toggle_deactivate_and_activate_other_user(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->mahasiswa()->create(['is_active' => true]);

        // Nonaktifkan
        $response = $this->actingAs($admin)->patch(route('admin.users.toggle', $student));
        $response->assertRedirect();
        $this->assertFalse($student->fresh()->is_active);

        // Aktifkan kembali
        $response = $this->actingAs($admin)->patch(route('admin.users.toggle', $student));
        $response->assertRedirect();
        $this->assertTrue($student->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->admin()->create(['is_active' => true]);

        // Coba toggle diri sendiri via route toggle
        $this->actingAs($admin)
            ->patch(route('admin.users.toggle', $admin))
            ->assertForbidden();

        $this->assertTrue($admin->fresh()->is_active);

        // Coba nonaktifkan diri sendiri via route update
        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => $admin->role,
                'is_active' => 0,
            ])
            ->assertForbidden();

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => User::ROLE_COACH,
            ])
            ->assertForbidden();

        $this->assertSame(User::ROLE_ADMIN, $admin->fresh()->role);
    }

    public function test_admin_cannot_deactivate_the_last_active_admin(): void
    {
        $admin1 = User::factory()->admin()->create(['is_active' => true]);
        $admin2 = User::factory()->admin()->create(['is_active' => false]);

        // Hanya ada 1 admin yang aktif ($admin1). Jika ada admin lain yang nonaktif, $admin1 tidak boleh dinonaktifkan.
        // Di sini kita uji coba jika ada admin aktif kedua dinonaktifkan hingga tersisa 1, lalu yang terakhir ditolak.
        $admin2->update(['is_active' => true]); // Sekarang ada 2 admin aktif

        // Admin 1 menonaktifkan Admin 2 -> Sukses
        $this->actingAs($admin1)->patch(route('admin.users.toggle', $admin2));
        $this->assertFalse($admin2->fresh()->is_active);

        // Sekarang hanya ada 1 admin aktif ($admin1). Jika dicoba dinonaktifkan, harus ditolak.
        $this->actingAs($admin1)->patch(route('admin.users.toggle', $admin1))->assertForbidden();
    }

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();

        // Coach
        $this->actingAs($coach)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($coach)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($coach)->get(route('admin.users.create'))->assertForbidden();
        $this->actingAs($coach)->post(route('admin.users.store'), [])->assertForbidden();
        $this->actingAs($coach)->patch(route('admin.users.toggle', $student))->assertForbidden();

        // Mahasiswa
        $this->actingAs($student)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($student)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($student)->get(route('admin.users.create'))->assertForbidden();
        $this->actingAs($student)->post(route('admin.users.store'), [])->assertForbidden();
        $this->actingAs($student)->patch(route('admin.users.toggle', $coach))->assertForbidden();
    }
}
