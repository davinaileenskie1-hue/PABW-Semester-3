<?php

use App\Enums\UserRole;
use App\Models\User;

test('the user factory creates a regular user by default', function () {
    expect(User::factory()->create()->role)->toBe(UserRole::User);
});

test('the admin factory state creates an admin', function () {
    expect(User::factory()->admin()->create()->role)->toBe(UserRole::Admin);
});

test('isAdmin only returns true for the admin role', function () {
    expect(User::factory()->admin()->create()->isAdmin())->toBeTrue()
        ->and(User::factory()->create()->isAdmin())->toBeFalse();
});

test('hasRole accepts enum and string roles', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    expect($admin->hasRole(UserRole::Admin, UserRole::User))->toBeTrue()
        ->and($admin->hasRole('admin'))->toBeTrue()
        ->and($admin->hasRole('user'))->toBeFalse()
        ->and($user->hasRole(UserRole::User))->toBeTrue()
        ->and($user->hasRole('admin'))->toBeFalse()
        ->and($user->hasRole('unknown-role'))->toBeFalse();
});

test('the role name is exposed as a label', function () {
    expect(UserRole::Admin->label())->toBe('Admin')
        ->and(UserRole::User->label())->toBe('User');
});

test('the role middleware rejects users without the required role', function () {
    Route::middleware(['web', 'role:admin'])->get('/test/admin-only', fn () => 'ok');

    $this->actingAs(User::factory()->create())
        ->get('/test/admin-only')
        ->assertForbidden();

    $this->actingAs(User::factory()->admin()->create())
        ->get('/test/admin-only')
        ->assertOk()
        ->assertSee('ok');
});

test('the role middleware accepts any of the listed roles', function () {
    Route::middleware(['web', 'role:admin,user'])->get('/test/any-role', fn () => 'ok');

    $this->actingAs(User::factory()->admin()->create())->get('/test/any-role')->assertOk();
    $this->actingAs(User::factory()->create())->get('/test/any-role')->assertOk();
});

test('the role middleware does not leak routes to guests', function () {
    Route::middleware(['web', 'role:admin'])->get('/test/guest-check', fn () => 'ok');

    $this->get('/test/guest-check')->assertForbidden();
});

test('new registrations always receive the user role', function () {
    $this->post('/register', [
        'name' => 'Pendaftar Baru',
        'email' => 'pendaftar@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    expect(User::where('email', 'pendaftar@example.com')->first()->role)->toBe(UserRole::User);
});

test('registration cannot be used to create an admin', function () {
    $this->post('/register', [
        'name' => 'Penyusup',
        'email' => 'penyusup@example.com',
        'role' => 'admin',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    expect(User::where('email', 'penyusup@example.com')->first()->role)->toBe(UserRole::User);
});
