<?php

declare(strict_types=1);

namespace App\Enums;

enum CourseType: string
{
    case Online = 'online';
    case Offline = 'offline';
    case Hybrid = 'hybrid';
    case Recorded = 'recorded';
}
