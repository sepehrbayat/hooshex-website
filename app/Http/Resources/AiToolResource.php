<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * AI Tool API Resource
 * 
 * @property-read \App\Domains\AiTools\Models\AiTool $resource
 */
class AiToolResource extends JsonResource
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
            'name' => $this->resource->name,
            'slug' => $this->resource->slug,
            'description' => $this->resource->description,
            'short_description' => $this->resource->short_description,
            'logo_url' => $this->resource->logo_path ? asset($this->resource->logo_path) : null,
            'website_url' => $this->resource->website_url,
            'pricing_type' => $this->resource->pricing_type,
            'pricing_details' => $this->resource->pricing_details,
            'is_featured' => $this->resource->is_featured,
            'is_verified' => $this->resource->is_verified,
            'rating' => $this->resource->rating,
            'published_at' => $this->resource->published_at?->toIso8601String(),
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
            'clicks_count' => $this->when(
                $this->resource->relationLoaded('clicks'),
                fn () => $this->resource->clicks_count ?? 0
            ),
            'links' => [
                'self' => route('api.v1.ai-tools.show', $this->resource->slug),
                'web' => route('ai-tools.show', $this->resource->slug),
            ],
        ];
    }
}
