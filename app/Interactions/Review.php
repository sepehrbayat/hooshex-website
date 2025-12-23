<?php

declare(strict_types=1);

namespace App\Interactions;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'reviewable_type',
        'reviewable_id',
        'rating',
        'title',
        'body',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'status' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}