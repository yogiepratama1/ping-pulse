<?php

namespace App\Filament\Resources\Monitors\Pages;

use App\Filament\Resources\Monitors\MonitorResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class ListMonitors extends ListRecords
{
    protected static string $resource = MonitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            self::StartJobAction(),
        ];
    }

    private static function StartJobAction()
    {
        return Action::make('startJob')
            ->label('Start Job')
            ->icon('heroicon-o-play')
            ->color('info')
            ->requiresConfirmation(fn() => Cache::has('monitor:dispatch:running'))
            ->modalHeading('Job Already In Progress')
            ->modalDescription('The monitoring dispatcher is currently running and processing monitors. Do you want to trigger another cycle anyway?')
            ->modalSubmitActionLabel('Yes, trigger again')
            ->action(function () {
                Artisan::call('monitor:dispatch');
            });
    }
}
