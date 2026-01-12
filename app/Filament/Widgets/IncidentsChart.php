<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class IncidentsChart extends ChartWidget
{
    protected ?string $heading = 'Incidents Chart';

    protected function getData(): array
    {
        $data = \Flowframe\Trend\Trend::model(\App\Models\Incident::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Incidents',
                    'data' => $data->map(fn(\Flowframe\Trend\TrendValue $value) => $value->aggregate),
                    'borderColor' => '#ef4444', // Danger red
                ],
            ],
            'labels' => $data->map(fn(\Flowframe\Trend\TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
