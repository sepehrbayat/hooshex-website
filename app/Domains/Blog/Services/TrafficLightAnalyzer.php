<?php

declare(strict_types=1);

namespace App\Domains\Blog\Services;

class TrafficLightAnalyzer
{
    public function analyze(string $content): array
    {
        $length = str($content)->length();
        $headings = preg_match_all('/<h[1-3][^>]*>/i', $content);
        $paragraphs = preg_match_all('/<p[^>]*>/i', $content);

        $score = 0;
        $score += min(30, $paragraphs * 3);
        $score += min(30, $headings * 10);
        $score += $length > 500 ? 20 : 0;
        $score = min(100, $score + 20);

        return [
            'score' => $score,
            'grade' => $this->grade($score),
            'details' => [
                'headings' => $headings,
                'paragraphs' => $paragraphs,
                'length' => $length,
            ],
        ];
    }

    private function grade(int $score): string
    {
        return match (true) {
            $score >= 80 => 'green',
            $score >= 50 => 'yellow',
            default => 'red',
        };
    }
}

