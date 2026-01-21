<?php

namespace App\Filament\Resources\Incidents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class IncidentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('monitor.alias')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('resolved_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('duration')
                    ->numeric()
                    ->suffix('s')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('open')
                    ->query(fn($query) => $query->whereNull('resolved_at')),
            ])
            ->recordActions([
                // View only
                ViewAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
