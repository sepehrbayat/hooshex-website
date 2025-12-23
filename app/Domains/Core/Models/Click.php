<?php

declare(strict_types=1);

namespace App\Domains\Core\Models;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Click extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_tool_id',
        'ip_address',
        'user_agent',
        'referer',
        'user_id',
        'clicked_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    public function aiTool(): BelongsTo
    {
        return $this->belongsTo(AiTool::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
