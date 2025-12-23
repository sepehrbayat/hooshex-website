<?php

declare(strict_types=1);

namespace App\Http\Controllers\Core;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Core\Services\ClickTracker;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClickController extends Controller
{
    public function __construct(
        private readonly ClickTracker $clickTracker
    ) {
    }

    public function go(string $slug, Request $request): RedirectResponse
    {
        $aiTool = AiTool::where('slug', $slug)->firstOrFail();

        // Track the click
        $this->clickTracker->track($aiTool, $request);

        // Redirect to affiliate_url if exists, otherwise website_url
        $targetUrl = $aiTool->affiliate_url ?? $aiTool->website_url;

        if (!$targetUrl) {
            abort(404);
        }

        // Use redirect()->away() for external URLs to prevent Laravel from trying to route them
        return redirect()->away($targetUrl);
    }
}