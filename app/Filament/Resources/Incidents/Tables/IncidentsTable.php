<?php

namespace App\Filament\Resources\Incidents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class IncidentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('monitor.alias')
                    ->searchable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('resolved_at')
                    ->dateTime()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('duration')
                    ->numeric()
                    ->suffix('s')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\Filter::make('open')
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
