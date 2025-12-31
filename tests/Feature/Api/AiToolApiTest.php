<?php

namespace Tests\Feature\Api;

use App\Domains\AiTools\Models\AiTool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiToolApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test AI tools index endpoint returns paginated results.
     */
    public function test_ai_tools_index_returns_paginated_results(): void
    {
        // Create some test AI tools
        AiTool::factory()->count(5)->create([
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/v1/ai-tools');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'links',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    /**
     * Test AI tools show endpoint returns single tool.
     */
    public function test_ai_tools_show_returns_single_tool(): void
    {
        $aiTool = AiTool::factory()->create([
            'published_at' => now()->subDay(),
            'slug' => 'test-tool',
        ]);

        $response = $this->getJson('/api/v1/ai-tools/test-tool');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $aiTool->id,
                    'name' => $aiTool->name,
                    'slug' => 'test-tool',
                ],
            ]);
    }

    /**
     * Test AI tools show endpoint returns 404 for non-existent tool.
     */
    public function test_ai_tools_show_returns_404_for_missing_tool(): void
    {
        $response = $this->getJson('/api/v1/ai-tools/non-existent');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'AI Tool not found',
            ]);
    }

    /**
     * Test AI tools featured endpoint returns featured tools.
     */
    public function test_ai_tools_featured_returns_featured_tools(): void
    {
        AiTool::factory()->count(3)->create([
            'published_at' => now()->subDay(),
            'is_featured' => true,
        ]);
        AiTool::factory()->count(2)->create([
            'published_at' => now()->subDay(),
            'is_featured' => false,
        ]);

        $response = $this->getJson('/api/v1/ai-tools/featured');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test AI tools can be filtered by pricing type.
     */
    public function test_ai_tools_can_be_filtered_by_pricing_type(): void
    {
        AiTool::factory()->create([
            'published_at' => now()->subDay(),
            'pricing_type' => 'free',
        ]);
        AiTool::factory()->create([
            'published_at' => now()->subDay(),
            'pricing_type' => 'paid',
        ]);

        $response = $this->getJson('/api/v1/ai-tools?pricing_type=free');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * Test unpublished AI tools are not returned.
     */
    public function test_unpublished_ai_tools_are_not_returned(): void
    {
        AiTool::factory()->create([
            'published_at' => null,
        ]);
        AiTool::factory()->create([
            'published_at' => now()->addDay(), // Future date
        ]);
        AiTool::factory()->create([
            'published_at' => now()->subDay(), // Published
        ]);

        $response = $this->getJson('/api/v1/ai-tools');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }
}
