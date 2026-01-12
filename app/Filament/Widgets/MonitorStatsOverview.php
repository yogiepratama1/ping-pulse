<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonitorStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Monitors', \App\Models\Monitor::count())
                ->icon('heroicon-o-computer-desktop'),
            Stat::make('Monitors Up', \App\Models\Monitor::where('status', 'up')->count())
                ->description('Operational')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Monitors Down', \App\Models\Monitor::where('status', 'down')->count())
                ->description('Outage')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}
