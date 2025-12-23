<?php

declare(strict_types=1);

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotFoundLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'referer',
        'ip_address',
        'user_agent',
        'hit_count',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'hit_count' => 'integer',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];
}
