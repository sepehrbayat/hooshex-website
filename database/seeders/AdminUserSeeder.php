<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Auth\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('app.admin_email', 'admin@example.com');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'username' => 'admin',
                'name' => 'Administrator',
                'password' => 'password', // hashed by cast
                'email_verified_at' => now(),
            ]
        );
        
        // Ensure role is set to admin
        $user->role = UserRole::Admin;
        $user->save();
    }
}

