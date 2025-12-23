<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name = '';
    public ?string $tagline = null;
    public ?string $logo_path = null;
    public ?string $favicon_path = null;
    public ?string $phone = null;
    public ?string $email = null;
    public ?string $address = null;
    public ?string $header_scripts = null;
    public ?string $footer_scripts = null;
    public ?array $social_profiles = null;

    public static function group(): string
    {
        return 'general';
    }
}