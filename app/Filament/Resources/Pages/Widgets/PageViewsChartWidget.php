<?php

namespace App\Filament\Resources\Pages\Widgets;

use Filament\Widgets\ChartWidget;

class PageViewsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Page Views Chart Widget';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
