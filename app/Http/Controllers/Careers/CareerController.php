<?php

declare(strict_types=1);

namespace App\Http\Controllers\Careers;

use App\Domains\Core\Models\Career;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CareerController extends Controller
{
    /**
     * Display the specified career/job posting.
     */
    public function show(string $slug): View
    {
        $career = Career::where('slug', $slug)
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        return view('careers.show', [
            'career' => $career,
        ]);
    }
}

