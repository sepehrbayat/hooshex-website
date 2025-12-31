<?php

declare(strict_types=1);

namespace App\Domains\AiTools\Actions;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Core\Models\Click;
use App\Domains\Auth\Models\User;
use Illuminate\Http\Request;

/**
 * Track Click Action
 * Records affiliate/website link clicks for AI tools
 */
class TrackClickAction
{
    /**
     * Execute the action
     *
     * @param AiTool $aiTool The AI tool being clicked
     * @param Request $request The HTTP request
     * @param User|null $user Optional authenticated user
     * @return Click The created click record
     */
    public function execute(AiTool $aiTool, Request $request, ?User $user = null): Click
    {
        return Click::create([
            'ai_tool_id' => $aiTool->id,
            'user_id' => $user?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'clicked_at' => now(),
        ]);
    }

    /**
     * Get the redirect URL for the AI tool
     */
    public function getRedirectUrl(AiTool $aiTool): string
    {
        return $aiTool->affiliate_url ?? $aiTool->website_url ?? '#';
    }
}
