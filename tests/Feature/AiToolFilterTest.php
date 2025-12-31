<?php

declare(strict_types=1);

use App\Domains\AiTools\Models\AiTool;
use App\Enums\PricingType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AiToolFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_filters_ai_tools_by_pricing_type(): void
    {
        AiTool::factory()->create(['name' => 'FreeTool', 'pricing_type' => PricingType::Free]);
        AiTool::factory()->create(['name' => 'PaidTool', 'pricing_type' => PricingType::Paid]);

        $response = $this->get('/ai-tools?pricing%5B0%5D=free');

        $response->assertSee('FreeTool');
    }
}

