<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Domains\Blog\Services\TrafficLightAnalyzer;
use Filament\Widgets\Widget;

class TrafficLightWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.traffic-light-widget';

    public ?string $content = null;

    public function getViewData(): array
    {
        $analysis = app(TrafficLightAnalyzer::class)->analyze($this->content ?? '');

        return [
            'analysis' => $analysis,
        ];
    }
}

