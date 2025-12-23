<?php

declare(strict_types=1);

namespace App\Enums;

enum WorkType: string
{
    case Remote = 'remote';
    case OnSite = 'on_site';
    case Hybrid = 'hybrid';

    public function label(): string
    {
        return match($this) {
            self::Remote => 'دورکار',
            self::OnSite => 'حضوری',
            self::Hybrid => 'هیبرید',
        };
    }
}

