<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Blog\Models\Post;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Posts API Controller
 */
class PostController extends Controller
{
    /**
     * List posts with optional filtering
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:255',
            'category' => 'nullable|string',
            'tag' => 'nullable|string',
            'author_id' => 'nullable|integer|exists:users,id',
            'sort' => 'nullable|string|in:latest,popular,views',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $query = Post::query()
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['author', 'categories', 'tags']);

        // Apply search
        if (!empty($validated['q'])) {
            $query->where(function ($q) use ($validated) {
                $q->where('title', 'like', "%{$validated['q']}%")
                  ->orWhere('excerpt', 'like', "%{$validated['q']}%")
                  ->orWhere('content', 'like', "%{$validated['q']}%");
            });
        }

        // Apply category filter
        if (!empty($validated['category'])) {
            $query->whereHas('categories', function ($q) use ($validated) {
                $q->where('slug', $validated['category']);
            });
        }

        // Apply tag filter
        if (!empty($validated['tag'])) {
            $query->withAnyTags([$validated['tag']]);
        }

        // Apply author filter
        if (!empty($validated['author_id'])) {
            $query->where('author_id', $validated['author_id']);
        }

        // Apply sorting
        $sort = $validated['sort'] ?? 'latest';
        $query = match ($sort) {
            'popular' => $query->orderByDesc('views_count'),
            'views' => $query->orderByDesc('views_count'),
            default => $query->orderByDesc('published_at'),
        };

        $perPage = (int) ($validated['per_page'] ?? 12);

        return PostResource::collection($query->paginate($perPage));
    }

    /**
     * Get a single post by slug
     */
    public function show(string $slug): PostResource|JsonResponse
    {
        $post = Post::query()
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('slug', $slug)
            ->with(['author', 'categories', 'tags'])
            ->first();

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        // Increment view count
        $post->increment('views_count');

        return new PostResource($post);
    }

    /**
     * Get featured/latest posts
     */
    public function featured(Request $request): AnonymousResourceCollection
    {
        $limit = min((int) ($request->query('limit', 6)), 12);
        
        $posts = Post::query()
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['author', 'categories'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();

        return PostResource::collection($posts);
    }

    /**
     * Get popular posts by view count
     */
    public function popular(Request $request): AnonymousResourceCollection
    {
        $limit = min((int) ($request->query('limit', 6)), 12);
        
        $posts = Post::query()
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['author', 'categories'])
            ->orderByDesc('views_count')
            ->limit($limit)
            ->get();

        return PostResource::collection($posts);
    }

    /**
     * Get related posts by category
     */
    public function related(string $slug, Request $request): AnonymousResourceCollection|JsonResponse
    {
        $post = Post::where('slug', $slug)->first();

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        $categoryIds = $post->categories()->pluck('categories.id');
        $limit = min((int) ($request->query('limit', 4)), 8);

        if ($categoryIds->isEmpty()) {
            return PostResource::collection(collect());
        }

        $related = Post::query()
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->with(['author', 'categories'])
            ->limit($limit)
            ->get();

        return PostResource::collection($related);
    }
}
