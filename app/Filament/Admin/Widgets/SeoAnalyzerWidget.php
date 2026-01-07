<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Domains\SEO\Contracts\SeoAnalyzerInterface;
use App\Domains\SEO\DTO\SeoAnalyzeInput;
use Filament\Widgets\Widget;

class SeoAnalyzerWidget extends Widget
{
    protected static string $view = 'filament.widgets.seo-analyzer';

    protected array $analysisResults = [];
    protected array $readabilityResults = [];
    protected int $seoScore = 0;
    protected int $readabilityScore = 0;

    private function seoAnalyzer(): SeoAnalyzerInterface
    {
        return app(SeoAnalyzerInterface::class);
    }

    public function analyze(?string $title = null, ?string $content = null, ?string $focusKeyword = null, ?string $metaDescription = null, ?string $slug = null): array
    {
        $analysis = $this->seoAnalyzer()->analyze(new SeoAnalyzeInput(
            title: $title,
            contentHtml: $content,
            focusKeyword: $focusKeyword,
            metaDescription: $metaDescription,
            slug: $slug,
            siteUrl: config('app.url'),
        ));

        $this->seoScore = $analysis->score;
        $this->analysisResults = $analysis->checks;

        return $analysis->toLegacyArray();
    }

    public function analyzeReadability(?string $content = null): array
    {
        $analysis = $this->seoAnalyzer()->analyzeReadability($content ?? '');

        $this->readabilityScore = $analysis->score;
        $this->readabilityResults = $analysis->results;

        return $analysis->toLegacyArray();
    }

    public function getScoreColor(int $score): string
    {
        if ($score >= 80) {
            return '#10b981'; // green
        } elseif ($score >= 50) {
            return '#f59e0b'; // orange
        } else {
            return '#ef4444'; // red
        }
    }

    public function getScoreLabel(int $score): string
    {
        if ($score >= 80) {
            return 'عالی';
        } elseif ($score >= 50) {
            return 'قابل قبول';
        } else {
            return 'نیاز به بهبود';
        }
    }
}
