<?php

use App\Enums\UserRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * Register an admin, log the current test in as them and return the admin.
 */
function actingAsAdmin(): User
{
    $admin = User::factory()->admin()->create();

    test()->actingAs($admin);

    return $admin;
}

test('guests are redirected to the login page', function (string $method, string $uri) {
    $this->{$method}($uri)->assertRedirect('/login');
})->with([
    ['get', '/admin/users'],
    ['get', '/admin/users/create'],
    ['post', '/admin/users'],
]);

test('regular users get a 403', function (string $method, string $uri) {
    $this->actingAs(User::factory()->create());

    $this->{$method}($uri)->assertForbidden();
})->with([
    ['get', '/admin/users'],
    ['get', '/admin/users/create'],
    ['post', '/admin/users'],
]);

test('admins can open the user index', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['name' => 'User Lain']);

    $this->actingAs($admin)
        ->get('/admin/users')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('UserManagement::index', shouldExist: false)
            ->where('users.data', fn ($users) => collect($users)->pluck('name')->contains('User Lain'))
            ->where('roles.0.value', UserRole::Admin->value)
            ->where('roles.0.label', 'Admin')
        );
});

test('the user index can be searched by name and email', function () {
    $this->actingAs(User::factory()->admin()->create());

    User::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@example.com']);
    User::factory()->create(['name' => 'Citra Dewi', 'email' => 'citra@example.com']);

    $this->get('/admin/users?search=budi')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('filters.search', 'budi')
            ->where('users.data', fn ($users) => collect($users)->pluck('name')->all() === ['Budi Santoso'])
        );

    $this->get('/admin/users?search=citra@example.com')
        ->assertInertia(fn (Assert $page) => $page
            ->where('users.data', fn ($users) => collect($users)->pluck('name')->all() === ['Citra Dewi'])
        );
});

test('the user index can be filtered by role', function () {
    $this->actingAs(User::factory()->admin()->create());

    User::factory()->create(['name' => 'User Biasa']);
    User::factory()->admin()->create(['name' => 'Admin Lain']);

    $this->get('/admin/users?role=admin')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('filters.role', 'admin')
            ->where('users.data', fn ($users) => collect($users)->pluck('role')->unique()->values()->all() === ['admin'])
        );
});

test('an invalid role filter is ignored', function () {
    $this->actingAs(User::factory()->admin()->create());
    User::factory()->create();

    $this->get('/admin/users?role=superadmin')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('filters.role', '')
            ->has('users.data', 2)
        );
});

test('admins can open the create and edit forms', function () {
    $user = User::factory()->create();

    $this->actingAs(User::factory()->admin()->create());

    $this->get('/admin/users/create')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('UserManagement::create', shouldExist: false)
            ->has('roles', 2)
        );

    $this->get("/admin/users/{$user->id}/edit")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('UserManagement::edit', shouldExist: false)
            ->where('user.id', $user->id)
            ->where('user.email', $user->email)
        );
});

test('an admin can create a new user', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->post('/admin/users', [
        'name' => 'User Baru',
        'email' => 'userbaru@example.com',
        'role' => 'admin',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('admin.users.index'));

    $created = User::where('email', 'userbaru@example.com')->first();

    expect($created)->not->toBeNull()
        ->and($created->role)->toBe(UserRole::Admin)
        ->and($created->name)->toBe('User Baru');
});

test('creating a user validates the input', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->post('/admin/users', [
        'name' => '',
        'email' => 'bukan-email',
        'role' => 'superadmin',
        'password' => 'short',
        'password_confirmation' => 'beda',
    ])->assertSessionHasErrors(['name', 'email', 'role', 'password']);

    expect(User::count())->toBe(1);
});

test('creating a user rejects a duplicate email', function () {
    User::factory()->create(['email' => 'sama@example.com']);

    $this->actingAs(User::factory()->admin()->create());

    $this->post('/admin/users', [
        'name' => 'Duplikat',
        'email' => 'sama@example.com',
        'role' => 'user',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('email');
});

test('an admin can update another user', function () {
    $this->actingAs(User::factory()->admin()->create());

    $user = User::factory()->create();

    $this->put("/admin/users/{$user->id}", [
        'name' => 'Nama Baru',
        'email' => 'namabaru@example.com',
        'role' => 'admin',
        'password' => '',
        'password_confirmation' => '',
    ])->assertRedirect(route('admin.users.index'));

    expect($user->fresh())
        ->name->toBe('Nama Baru')
        ->email->toBe('namabaru@example.com')
        ->role->toBe(UserRole::Admin);
});

test('updating without a password keeps the old password', function () {
    $this->actingAs(User::factory()->admin()->create());

    $user = User::factory()->create();
    $oldPassword = $user->password;

    $this->put("/admin/users/{$user->id}", [
        'name' => $user->name,
        'email' => $user->email,
        'role' => 'user',
        'password' => '',
        'password_confirmation' => '',
    ]);

    expect($user->fresh()->password)->toBe($oldPassword);
});

test('updating with a password changes it', function () {
    $this->actingAs(User::factory()->admin()->create());

    $user = User::factory()->create();
    $oldPassword = $user->password;

    $this->put("/admin/users/{$user->id}", [
        'name' => $user->name,
        'email' => $user->email,
        'role' => 'user',
        'password' => 'password-baru',
        'password_confirmation' => 'password-baru',
    ]);

    expect($user->fresh()->password)->not->toBe($oldPassword);
});

test('an admin cannot demote their own account', function () {
    $admin = actingAsAdmin();

    $this->put("/admin/users/{$admin->id}", [
        'name' => $admin->name,
        'email' => $admin->email,
        'role' => 'user',
        'password' => '',
        'password_confirmation' => '',
    ])->assertSessionHasErrors('role');

    expect($admin->fresh()->role)->toBe(UserRole::Admin);
});

test('an admin can update their own profile details', function () {
    $admin = actingAsAdmin();

    $this->put("/admin/users/{$admin->id}", [
        'name' => 'Admin Diperbarui',
        'email' => $admin->email,
        'role' => 'admin',
        'password' => '',
        'password_confirmation' => '',
    ])->assertRedirect(route('admin.users.index'));

    expect($admin->fresh()->name)->toBe('Admin Diperbarui');
});

test('an admin can delete another user', function () {
    $this->actingAs(User::factory()->admin()->create());

    $user = User::factory()->create();

    $this->delete("/admin/users/{$user->id}")
        ->assertRedirect(route('admin.users.index'));

    expect(User::find($user->id))->toBeNull();
});

test('an admin cannot delete their own account', function () {
    $admin = actingAsAdmin();

    $this->delete("/admin/users/{$admin->id}")
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('error');

    expect(User::find($admin->id))->not->toBeNull();
});
