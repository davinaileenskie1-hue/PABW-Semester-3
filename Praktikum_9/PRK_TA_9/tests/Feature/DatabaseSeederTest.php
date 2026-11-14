<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;

test('the seeder creates one admin and one regular user', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::count())->toBe(2);

    $admin = User::where('email', 'admin@example.com')->first();
    $user = User::where('email', 'user@example.com')->first();

    expect($admin->role)->toBe(UserRole::Admin)
        ->and($admin->isAdmin())->toBeTrue()
        ->and($user->role)->toBe(UserRole::User)
        ->and(Hash::check('password', $admin->password))->toBeTrue()
        ->and(Hash::check('password', $user->password))->toBeTrue();
});
