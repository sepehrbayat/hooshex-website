<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\AiTools\Services\AiToolSearchService;
use App\Http\Controllers\Controller;
use App\Http\Resources\AiToolResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * AI Tools API Controller
 */
class AiToolController extends Controller
{
    public function __construct(
        private readonly AiToolSearchService $searchService
    ) {
    }

    /**
     * List AI tools with optional filtering
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:255',
            'category' => 'nullable|string',
            'pricing_type' => 'nullable|string|in:free,freemium,paid',
            'verified' => 'nullable|boolean',
            'sort' => 'nullable|string|in:latest,popular,rating,name',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $aiTools = $this->searchService->search(
            query: $validated['q'] ?? null,
            filters: $validated,
            perPage: (int) ($validated['per_page'] ?? 12)
        );

        return AiToolResource::collection($aiTools);
    }

    /**
     * Get a single AI tool by slug
     */
    public function show(string $slug): AiToolResource|JsonResponse
    {
        $aiTool = AiTool::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('slug', $slug)
            ->with(['categories', 'tags'])
            ->first();

        if (!$aiTool) {
            return response()->json([
                'message' => 'AI Tool not found',
            ], 404);
        }

        return new AiToolResource($aiTool);
    }

    /**
     * Get featured AI tools
     */
    public function featured(Request $request): AnonymousResourceCollection
    {
        $limit = min((int) ($request->query('limit', 6)), 12);
        
        return AiToolResource::collection(
            $this->searchService->getFeatured($limit)
        );
    }

    /**
     * Get popular AI tools
     */
    public function popular(Request $request): AnonymousResourceCollection
    {
        $limit = min((int) ($request->query('limit', 6)), 12);
        
        return AiToolResource::collection(
            $this->searchService->getPopular($limit)
        );
    }

    /**
     * Get related AI tools
     */
    public function related(string $slug, Request $request): AnonymousResourceCollection|JsonResponse
    {
        $aiTool = AiTool::where('slug', $slug)->first();

        if (!$aiTool) {
            return response()->json([
                'message' => 'AI Tool not found',
            ], 404);
        }

        $limit = min((int) ($request->query('limit', 4)), 8);
        
        return AiToolResource::collection(
            $this->searchService->getRelated($aiTool, $limit)
        );
    }
}
