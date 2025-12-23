<?php

declare(strict_types=1);

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

class Page extends Model
{
    use HasFactory;
    use HasSEO;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content_blocks',
        'template',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'content_blocks' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];
}