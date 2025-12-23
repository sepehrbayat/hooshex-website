<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domains\Courses\Models\Course;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsEnrolled
{
    public function handle(Request $request, Closure $next): Response
    {
        $courseId = (int) $request->route('course');
        $user = $request->user();

        if (! $user || ! $courseId) {
            abort(403);
        }

        $enrolled = $user->enrollments()
            ->where('course_id', $courseId)
            ->exists();

        abort_unless($enrolled, 403);

        return $next($request);
    }
}

