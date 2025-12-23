<?php

declare(strict_types=1);

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'title',
        'video_url',
        'duration',
        'is_free_preview',
        'is_free',
        'sort_order',
        'content',
    ];

    protected $casts = [
        'is_free_preview' => 'boolean',
        'is_free' => 'boolean',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(\App\Interactions\Comment::class, 'commentable');
    }
}

