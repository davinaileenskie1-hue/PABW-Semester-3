<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_halaman_login_saat_membuka_daftar_laporan(): void
    {
        $this->get('/laporan')->assertRedirect(route('login'));
    }

    public function test_form_pelaporan_tetap_dapat_diakses_tanpa_login(): void
    {
        $this->get('/laporan/create')->assertOk();
    }

    public function test_petugas_dapat_login_dan_membuka_halaman_yang_dilindungi(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get('/laporan')->assertOk();
        $this->get('/profile')->assertOk()->assertSee($user->email);
    }

    public function test_login_gagal_menampilkan_pesan_galat(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $this->from('/login')
            ->post('/login', [
                'email' => $user->email,
                'password' => 'password-salah',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_petugas_dapat_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
