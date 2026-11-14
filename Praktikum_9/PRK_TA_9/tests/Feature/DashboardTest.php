<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Buku\Models\Buku;
use Modules\Peminjaman\Models\Peminjaman;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertOk();
});

test('the dashboard shows the number of books and loans', function () {
    Buku::factory()->count(3)->create();
    Peminjaman::factory()->create(['status' => 'Dipinjam']);
    Peminjaman::factory()->create(['status' => 'Dikembalikan']);

    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard', shouldExist: false)
            ->where('totalBuku', 3)
            ->where('totalPeminjaman', 2)
            ->where('totalDipinjam', 1)
        );
});

test('the total number of users is only shared with admins', function () {
    User::factory()->count(4)->create();

    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page->where('totalUsers', null));

    $this->actingAs(User::factory()->admin()->create())
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page->where('totalUsers', User::count()));
});

test('the shared auth prop exposes the authenticated user and role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page
            ->where('auth.user.id', $admin->id)
            ->where('auth.user.email', $admin->email)
            ->where('auth.user.role', 'admin')
            ->has('flash')
        );
});
