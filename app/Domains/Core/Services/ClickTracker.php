<?php

declare(strict_types=1);

namespace App\Domains\Core\Services;

use App\Domains\Core\Models\Click;
use App\Domains\AiTools\Models\AiTool;
use Illuminate\Http\Request;

class ClickTracker
{
    public function track(AiTool $aiTool, Request $request): void
    {
        Click::create([
            'ai_tool_id' => $aiTool->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'user_id' => auth()->id(),
            'clicked_at' => now(),
        ]);
    }

    public function getClickCount(int $aiToolId): int
    {
        return Click::where('ai_tool_id', $aiToolId)->count();
    }
}
