<?php

declare(strict_types=1);

namespace App\Http\Controllers\Courses;

use App\Domains\Courses\Models\Course;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display the specified course.
     */
    public function show(string $slug): View
    {
        $course = Course::where('slug', $slug)
            ->where('status', \App\Enums\CourseStatus::Published)
            ->with(['teacher', 'chapters.lessons', 'thumbnail'])
            ->firstOrFail();

        return view('courses.show', [
            'course' => $course,
        ]);
    }
}
