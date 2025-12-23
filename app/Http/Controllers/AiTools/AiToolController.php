<?php

declare(strict_types=1);

namespace App\Http\Controllers\AiTools;

use App\Domains\AiTools\Models\AiTool;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AiToolController extends Controller
{
    /**
     * Display the specified AI tool.
     */
    public function show(string $slug): View
    {
        $aiTool = AiTool::where('slug', $slug)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['logo', 'categories'])
            ->firstOrFail();

        return view('ai-tools.show', [
            'aiTool' => $aiTool,
        ]);
    }
}
