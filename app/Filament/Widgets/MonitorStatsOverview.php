<?php

namespace App\Filament\Widgets;

use App\Models\Monitor;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonitorStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Monitors', Monitor::count())
                ->icon('heroicon-o-computer-desktop'),
            Stat::make('Monitors Up', Monitor::where('status', 'up')->count())
                ->description('Operational')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Monitors Down', Monitor::where('status', 'down')->count())
                ->description('Outage')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}
