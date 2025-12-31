<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Services\CourseSearchService;
use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Courses API Controller
 */
class CourseController extends Controller
{
    public function __construct(
        private readonly CourseSearchService $searchService
    ) {
    }

    /**
     * List courses with optional filtering
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:255',
            'category' => 'nullable|string',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'teacher_id' => 'nullable|integer|exists:teachers,id',
            'free' => 'nullable|boolean',
            'has_certificate' => 'nullable|boolean',
            'sort' => 'nullable|string|in:latest,popular,price_low,price_high,duration',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $courses = $this->searchService->search(
            query: $validated['q'] ?? null,
            filters: $validated,
            perPage: (int) ($validated['per_page'] ?? 12)
        );

        return CourseResource::collection($courses);
    }

    /**
     * Get a single course by slug
     */
    public function show(string $slug): CourseResource|JsonResponse
    {
        $course = Course::query()
            ->where('status', CourseStatus::Published)
            ->where('slug', $slug)
            ->with(['teacher.teacherProfile', 'categories', 'thumbnail', 'chapters.lessons'])
            ->first();

        if (!$course) {
            return response()->json([
                'message' => 'Course not found',
            ], 404);
        }

        return new CourseResource($course);
    }

    /**
     * Get featured courses
     */
    public function featured(Request $request): AnonymousResourceCollection
    {
        $limit = min((int) ($request->query('limit', 6)), 12);
        
        return CourseResource::collection(
            $this->searchService->getFeatured($limit)
        );
    }

    /**
     * Get popular courses
     */
    public function popular(Request $request): AnonymousResourceCollection
    {
        $limit = min((int) ($request->query('limit', 6)), 12);
        
        return CourseResource::collection(
            $this->searchService->getPopular($limit)
        );
    }

    /**
     * Get related courses
     */
    public function related(string $slug, Request $request): AnonymousResourceCollection|JsonResponse
    {
        $course = Course::where('slug', $slug)->first();

        if (!$course) {
            return response()->json([
                'message' => 'Course not found',
            ], 404);
        }

        $limit = min((int) ($request->query('limit', 4)), 8);
        
        return CourseResource::collection(
            $this->searchService->getRelated($course, $limit)
        );
    }
}
