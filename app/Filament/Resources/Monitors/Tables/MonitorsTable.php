<?php

namespace App\Filament\Resources\Monitors\Tables;

use App\Models\Monitor;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Jobs\PerformUptimeCheck;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class MonitorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'up' => 'success',
                        'down' => 'danger',
                        'degraded' => 'warning',
                        'pending' => 'gray',
                    }),

                TextColumn::make('alias')
                    ->description(fn(Monitor $record) => $record->url)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('region')
                    ->label('Region')
                    ->state(fn(Monitor $record) => $record->region ?? 'N/A'),

                TextColumn::make('tags.name')
                    ->badge()
                    ->color(fn($record) => 'info'),

                TextColumn::make('avg_ping')
                    ->label('Avg Ping')
                    ->state(fn($record) => $record->monitorLogs()->latest()->first()?->avg_response_time_ms ? $record->monitorLogs()->latest()->first()?->avg_response_time_ms . ' ms' : '-')
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray'),

                TextColumn::make('uptime_percentage')
                    ->label('Uptime (7d)')
                    ->suffix('%'),

                TextColumn::make('last_checked_at')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'up' => 'Up',
                        'down' => 'Down',
                        'degraded' => 'Degraded',
                        'pending' => 'Pending',
                    ]),
                SelectFilter::make('tags')
                    ->relationship('tags', 'name'),
            ])
            ->recordActions([
                Action::make('test')
                    ->label('Test Now')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (Monitor $record) {
                        // Dispatch job (to be created)
                        PerformUptimeCheck::dispatch($record);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
