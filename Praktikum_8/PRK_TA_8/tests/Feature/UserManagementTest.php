<?php

use App\Enums\UserRole;
use App\Models\User;

it('menampilkan daftar user untuk admin', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['name' => 'Anggota Satu']);

    $this->actingAs($admin)->get('/admin/users')
        ->assertOk()
        ->assertSee('Anggota Satu')
        ->assertSee('Anda');
});

it('menambah user baru beserta role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/admin/users', [
        'name' => 'Petugas Baru',
        'email' => 'petugas@example.com',
        'role' => 'admin',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect('/admin/users')->assertSessionHas('success');

    $user = User::where('email', 'petugas@example.com')->firstOrFail();
    expect($user->role)->toBe(UserRole::Admin);
});

it('menolak role yang tidak dikenal', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->from('/admin/users/create')->post('/admin/users', [
        'name' => 'Super',
        'email' => 'super@example.com',
        'role' => 'superadmin',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect('/admin/users/create')->assertSessionHasErrors(['role']);
});

it('tidak mengubah password ketika kolom password dikosongkan saat edit', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['password' => 'password']);
    $lama = $user->password;

    $this->actingAs($admin)->put("/admin/users/{$user->id}", [
        'name' => 'Nama Diubah',
        'email' => $user->email,
        'role' => 'user',
        'password' => '',
        'password_confirmation' => '',
    ])->assertRedirect('/admin/users');

    $user->refresh();
    expect($user->name)->toBe('Nama Diubah')
        ->and($user->password)->toBe($lama);
});

it('mencegah admin menurunkan role akunnya sendiri', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->from("/admin/users/{$admin->id}/edit")->put("/admin/users/{$admin->id}", [
        'name' => $admin->name,
        'email' => $admin->email,
        'role' => 'user',
    ])->assertRedirect("/admin/users/{$admin->id}/edit")->assertSessionHasErrors(['role']);

    expect($admin->fresh()->isAdmin())->toBeTrue();
});

it('mencegah admin menghapus akunnya sendiri', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->delete("/admin/users/{$admin->id}")
        ->assertRedirect('/admin/users')
        ->assertSessionHas('error');

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

it('menghapus user lain', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)->delete("/admin/users/{$user->id}")
        ->assertRedirect('/admin/users')
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
