<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_routes_return_404(): void
    {
        $this->get('/register')->assertNotFound();

        $this->post('/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertNotFound();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/coach/dashboard')->assertRedirect('/login');
        $this->get('/mahasiswa/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_user_visiting_home_redirects_to_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/')->assertRedirect(route('dashboard'));
    }

    public function test_dashboard_redirects_admin_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_dashboard_redirects_coach_to_coach_dashboard(): void
    {
        $coach = User::factory()->coach()->create();

        $response = $this->actingAs($coach)->get('/dashboard');

        $response->assertRedirect(route('coach.dashboard'));
    }

    public function test_dashboard_redirects_mahasiswa_to_mahasiswa_dashboard(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();

        $response = $this->actingAs($mahasiswa)->get('/dashboard');

        $response->assertRedirect(route('mahasiswa.dashboard'));
    }

    public function test_admin_cannot_access_coach_or_mahasiswa_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('coach.dashboard'))->assertForbidden();
        $this->actingAs($admin)->get(route('mahasiswa.dashboard'))->assertForbidden();
    }

    public function test_coach_cannot_access_admin_or_mahasiswa_dashboard(): void
    {
        $coach = User::factory()->coach()->create();

        $this->actingAs($coach)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($coach)->get(route('mahasiswa.dashboard'))->assertForbidden();
    }

    public function test_mahasiswa_cannot_access_admin_or_coach_dashboard(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->actingAs($mahasiswa)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('coach.dashboard'))->assertForbidden();
    }

    public function test_inactive_user_is_logged_out_and_redirected_to_login(): void
    {
        $inactiveUser = User::factory()->inactive()->create();

        $response = $this->actingAs($inactiveUser)->get('/dashboard');

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);
    }

    public function test_custom_403_and_404_error_pages_rendered_in_indonesian(): void
    {
        $admin = User::factory()->admin()->create();

        $forbidden = $this->actingAs($admin)->get(route('coach.dashboard'));
        $forbidden->assertForbidden();
        $forbidden->assertSee('Akses Ditolak');
        $forbidden->assertSee('Kesalahan 403');

        $notFound = $this->get('/halaman-yang-pasti-tidak-ada-9999');
        $notFound->assertNotFound();
        $notFound->assertSee('Halaman Tidak Ditemukan');
        $notFound->assertSee('Kesalahan 404');
    }
}
