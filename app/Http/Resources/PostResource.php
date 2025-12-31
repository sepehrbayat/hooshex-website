<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Post API Resource
 * 
 * @property-read \App\Domains\Blog\Models\Post $resource
 */
class PostResource extends JsonResource
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
            'excerpt' => $this->resource->excerpt,
            'content' => $this->when(
                $request->routeIs('*.show'),
                fn () => $this->resource->content
            ),
            'thumbnail_url' => $this->resource->thumbnail_path 
                ? asset($this->resource->thumbnail_path) 
                : null,
            'reading_time' => $this->resource->reading_time,
            'views_count' => $this->resource->views_count ?? 0,
            'status' => $this->resource->status->value,
            'published_at' => $this->resource->published_at?->toIso8601String(),
            'author' => $this->whenLoaded('author', function () {
                return [
                    'id' => $this->resource->author->id,
                    'name' => $this->resource->author->name,
                ];
            }),
            'categories' => $this->whenLoaded('categories', function () {
                return $this->resource->categories->map(fn ($cat) => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                ]);
            }),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->resource->tags->pluck('name');
            }),
            'links' => [
                'self' => route('api.v1.posts.show', $this->resource->slug),
                'web' => route('posts.show', $this->resource->slug),
            ],
        ];
    }
}
