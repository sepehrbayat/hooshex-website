<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Core\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display a page by slug
     */
    public function show(Request $request, string $slug): View
    {
        $page = Page::where('slug', $slug)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        return view('pages.show', [
            'page' => $page,
        ]);
    }
}