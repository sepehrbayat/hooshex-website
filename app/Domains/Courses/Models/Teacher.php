<?php

declare(strict_types=1);

namespace App\Domains\Courses\Models;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

class Teacher extends Model
{
    use HasFactory;
    use HasSEO;

    protected $fillable = [
        'user_id',
        'slug',
        'bio',
        'specialty',
        'social_links',
        'avatar_path',
        'avatar_id',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id', 'user_id');
    }

    public function avatar(): BelongsTo
    {
        return $this->belongsTo(\Awcodes\Curator\Models\Media::class, 'avatar_id');
    }
}