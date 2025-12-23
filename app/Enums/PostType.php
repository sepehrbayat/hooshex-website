<?php

declare(strict_types=1);

namespace App\Enums;

enum PostType: string
{
    case Article = 'article';
    case News = 'news';
}

