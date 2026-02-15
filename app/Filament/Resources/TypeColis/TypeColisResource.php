<?php

namespace App\Filament\Resources\TypeColis;

use App\Filament\Resources\TypeColis\Pages\CreateTypeColis;
use App\Filament\Resources\TypeColis\Pages\EditTypeColis;
use App\Filament\Resources\TypeColis\Pages\ListTypeColis;
use App\Filament\Resources\TypeColis\Schemas\TypeColisForm;
use App\Filament\Resources\TypeColis\Tables\TypeColisTable;
use App\Models\TypeColis;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TypeColisResource extends Resource
{
    protected static ?string $model = TypeColis::class;

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string | UnitEnum | null $navigationGroup = 'Paramètres';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'App/Models/TypeColis';

    public static function form(Schema $schema): Schema
    {
        return TypeColisForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TypeColisTable::configure($table);
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
            'index' => ListTypeColis::route('/'),
            'create' => CreateTypeColis::route('/create'),
            'edit' => EditTypeColis::route('/{record}/edit'),
        ];
    }
        public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
