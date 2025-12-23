<?php

declare(strict_types=1);

use App\Domains\AiTools\Models\AiTool;
use App\Enums\PricingType;

it('filters ai tools by pricing type', function () {
    AiTool::factory()->create(['name' => 'FreeTool', 'pricing_type' => PricingType::Free]);
    AiTool::factory()->create(['name' => 'PaidTool', 'pricing_type' => PricingType::Paid]);

    $response = $this->get('/ai-tools?pricing%5B0%5D=free');

    $response->assertSee('FreeTool');
});

