<?php

namespace App\Filament\Resources\Tags;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Tag;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return \App\Filament\Resources\Tags\Schemas\TagForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return \App\Filament\Resources\Tags\Tables\TagsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Tags\Pages\ListTags::route('/'),
            'create' => \App\Filament\Resources\Tags\Pages\CreateTag::route('/create'),
            'edit' => \App\Filament\Resources\Tags\Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
