<?php

namespace App\Filament\Widgets;

use App\Models\Analytic;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DownloadStatisticsWidget extends ChartWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = '30';

    public function getHeading(): ?string
    {
        return 'Research Downloads';
    }

    protected function getData(): array
    {
        $days = (int) $this->filter;
        $data = [];
        $labels = [];

        // Get downloads for the specified period
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');
            
            $count = Analytic::researchDownloads()
                ->whereDate('created_at', $date)
                ->count();
            
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Downloads',
                    'data' => $data,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
            '30' => 'Last 30 days',
            '90' => 'Last 90 days',
        ];
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
