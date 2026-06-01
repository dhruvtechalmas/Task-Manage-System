<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\UserSeeder;

test('user seeder creates one super admin and one employee', function () {
    $this->seed(UserSeeder::class);

    expect(User::role(UserRole::SuperAdmin->value)->count())->toBe(1)
        ->and(User::role(UserRole::Employee->value)->count())->toBe(1)
        ->and(User::count())->toBe(2);
});
