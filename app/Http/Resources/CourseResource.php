<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Course API Resource
 * 
 * @property-read \App\Domains\Courses\Models\Course $resource
 */
class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'slug' => $this->resource->slug,
            'short_description' => $this->resource->short_description,
            'description' => $this->when(
                $request->routeIs('*.show'),
                fn () => $this->resource->description
            ),
            'thumbnail_url' => $this->resource->thumbnail_path 
                ? asset($this->resource->thumbnail_path) 
                : null,
            'price' => $this->resource->price,
            'discounted_price' => $this->resource->discounted_price,
            'level' => $this->resource->level,
            'duration' => $this->resource->duration,
            'lessons_count' => $this->resource->lessons_count ?? 0,
            'is_featured' => $this->resource->is_featured,
            'is_certificate_available' => $this->resource->is_certificate_available,
            'status' => $this->resource->status->value,
            'published_at' => $this->resource->published_at?->toIso8601String(),
            'teacher' => $this->whenLoaded('teacher', function () {
                return [
                    'id' => $this->resource->teacher->id,
                    'name' => $this->resource->teacher->user?->name,
                    'slug' => $this->resource->teacher->slug,
                    'avatar_url' => $this->resource->teacher->avatar_path 
                        ? asset($this->resource->teacher->avatar_path) 
                        : null,
                ];
            }),
            'categories' => $this->whenLoaded('categories', function () {
                return $this->resource->categories->map(fn ($cat) => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                ]);
            }),
            'chapters' => $this->when(
                $request->routeIs('*.show') && $this->resource->relationLoaded('chapters'),
                function () {
                    return $this->resource->chapters->map(fn ($chapter) => [
                        'id' => $chapter->id,
                        'title' => $chapter->title,
                        'order' => $chapter->order,
                        'lessons' => $chapter->lessons->map(fn ($lesson) => [
                            'id' => $lesson->id,
                            'title' => $lesson->title,
                            'duration' => $lesson->duration,
                            'is_free' => $lesson->is_free,
                            'order' => $lesson->order,
                        ]),
                    ]);
                }
            ),
            'enrollments_count' => $this->when(
                isset($this->resource->enrollments_count),
                fn () => $this->resource->enrollments_count
            ),
            'links' => [
                'self' => route('api.v1.courses.show', $this->resource->slug),
                'web' => route('courses.show', $this->resource->slug),
            ],
        ];
    }
}
