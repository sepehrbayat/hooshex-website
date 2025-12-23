<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $group = 'general';
        $defaults = [
            'site_name' => config('app.name', 'هوشکس'),
            'tagline' => '',
            'logo_path' => null,
            'favicon_path' => null,
            'phone' => null,
            'email' => null,
            'address' => null,
            'header_scripts' => null,
            'footer_scripts' => null,
            'social_profiles' => [],
        ];

        foreach ($defaults as $name => $value) {
            // Encode value as JSON payload (Spatie Laravel Settings format)
            // For null values, use empty string for strings, empty array for arrays
            if ($value === null) {
                $payload = $name === 'social_profiles' ? json_encode([]) : json_encode('');
            } else {
                $payload = json_encode($value);
            }
            
            DB::table('settings')->updateOrInsert(
                [
                    'name' => $name,
                    'group' => $group,
                ],
                [
                    'payload' => $payload,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
