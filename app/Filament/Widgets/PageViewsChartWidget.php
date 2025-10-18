<?php

namespace App\Filament\Widgets;

use App\Models\Analytic;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PageViewsChartWidget extends ChartWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return 'Page Views Over Time';
    }

    protected function getData(): array
    {
        $days = 30;
        $data = [];
        $labels = [];

        // Get page views for the last 30 days
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');
            
            $count = Analytic::pageViews()
                ->whereDate('created_at', $date)
                ->count();
            
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Page Views',
                    'data' => $data,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
