<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Auth\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'user@test.com'],
            [
                'username' => 'testuser',
                'name' => 'کاربر تست',
                'password' => 'password', // hashed by cast
                'email_verified_at' => now(),
                'role' => UserRole::Student,
            ]
        );

        $this->command->info('Test user created:');
        $this->command->info('Email: user@test.com');
        $this->command->info('Password: password');
    }
}
