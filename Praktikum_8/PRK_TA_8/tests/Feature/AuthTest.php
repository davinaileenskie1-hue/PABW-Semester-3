<?php

use App\Enums\UserRole;
use App\Models\User;

it('mengarahkan tamu ke halaman login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
    $this->get('/buku')->assertRedirect('/login');
    $this->get('/peminjaman')->assertRedirect('/login');
    $this->get('/admin/users')->assertRedirect('/login');
});

it('mendaftarkan akun baru sebagai user dan langsung login', function () {
    $response = $this->post('/register', [
        'name' => 'Calon Anggota',
        'email' => 'calon@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();

    $user = User::where('email', 'calon@example.com')->firstOrFail();
    expect($user->role)->toBe(UserRole::User);
});

it('menolak register dengan password tidak valid', function () {
    $this->from('/register')->post('/register', [
        'name' => 'Salah',
        'email' => 'salah@example.com',
        'password' => '123',
        'password_confirmation' => '456',
    ])->assertRedirect('/register')->assertSessionHasErrors(['password']);

    $this->assertGuest();
});

it('menolak pendaftar yang mencoba mengisi role admin', function () {
    $this->post('/register', [
        'name' => 'Curang',
        'email' => 'curang@example.com',
        'role' => 'admin',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    expect(User::where('email', 'curang@example.com')->firstOrFail()->isAdmin())->toBeFalse();
});

it('login dan logout', function () {
    $user = User::factory()->create(['password' => 'password']);

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);

    $this->post('/logout')->assertRedirect('/login');
    $this->assertGuest();
});

it('mengunci login setelah lima kali gagal', function () {
    $user = User::factory()->create(['password' => 'password']);

    foreach (range(1, 5) as $attempt) {
        $this->post('/login', ['email' => $user->email, 'password' => 'salah']);
    }

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors(['email']);

    $this->assertGuest();
});

it('menolak user membuka halaman khusus admin dengan 403', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin/users')->assertForbidden();
    $this->actingAs($user)->get('/buku/create')->assertForbidden();
});

it('mengizinkan admin membuka halaman kelola user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/admin/users')->assertOk();
    $this->actingAs($admin)->get('/dashboard')->assertOk()->assertSee('Total User');
});
