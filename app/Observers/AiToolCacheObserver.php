<?php

declare(strict_types=1);

namespace App\Observers;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\AiTools\Services\AiToolSearchService;
use Illuminate\Support\Facades\Cache;

/**
 * Observer to invalidate AiTool cache on model changes
 */
class AiToolCacheObserver
{
    /**
     * Handle the AiTool "saved" event (created or updated).
     */
    public function saved(AiTool $aiTool): void
    {
        $this->clearCache($aiTool);
    }

    /**
     * Handle the AiTool "deleted" event.
     */
    public function deleted(AiTool $aiTool): void
    {
        $this->clearCache($aiTool);
    }

    /**
     * Clear caches related to this AiTool
     */
    private function clearCache(AiTool $aiTool): void
    {
        // Clear featured/popular caches
        AiToolSearchService::clearCache();

        // Clear related cache for this tool
        foreach ([3, 4, 6, 8] as $limit) {
            Cache::forget("aitools:related:{$aiTool->id}:{$limit}");
        }
    }
}
