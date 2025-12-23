<?php

declare(strict_types=1);

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_url',
        'target_url',
        'status_code',
        'hit_count',
        'last_accessed_at',
    ];

    protected $casts = [
        'hit_count' => 'integer',
        'status_code' => 'integer',
        'last_accessed_at' => 'datetime',
    ];

    /**
     * Increment hit count and update last accessed timestamp
     */
    public function recordHit(): void
    {
        $this->increment('hit_count');
        $this->update(['last_accessed_at' => now()]);
    }
}
