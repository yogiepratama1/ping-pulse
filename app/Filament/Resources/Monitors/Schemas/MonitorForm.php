<?php

namespace App\Filament\Resources\Monitors\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;

class MonitorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn() => auth()->id()),

                TextInput::make('url')
                    ->required()
                    ->url()
                    ->columnSpanFull(),

                TextInput::make('alias')
                    ->required(),

                Select::make('check_interval')
                    ->options([
                        60 => '1 Minute',
                        300 => '5 Minutes',
                        1800 => '30 Minutes',
                        3600 => '1 Hour',
                    ])
                    ->default(300)
                    ->required(),

                TextInput::make('max_retries')
                    ->numeric()
                    ->default(3)
                    ->required(),

                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                        ColorPicker::make('color')->required(),
                    ]),
                TextInput::make('region')
            ]);
    }
}
