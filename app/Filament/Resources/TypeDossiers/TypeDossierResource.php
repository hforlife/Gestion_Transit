<?php

namespace App\Filament\Resources\TypeDossiers;

use App\Filament\Resources\TypeDossiers\Pages\CreateTypeDossier;
use App\Filament\Resources\TypeDossiers\Pages\EditTypeDossier;
use App\Filament\Resources\TypeDossiers\Pages\ListTypeDossiers;
use App\Filament\Resources\TypeDossiers\Schemas\TypeDossierForm;
use App\Filament\Resources\TypeDossiers\Tables\TypeDossiersTable;
use App\Models\TypeDossier;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TypeDossierResource extends Resource
{
    protected static ?string $model = TypeDossier::class;

    protected static string | UnitEnum | null $navigationGroup = 'Paramètres';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = ' App/Models/TypeDossier';

    public static function form(Schema $schema): Schema
    {
        return TypeDossierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TypeDossiersTable::configure($table);
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
            'index' => ListTypeDossiers::route('/'),
            'create' => CreateTypeDossier::route('/create'),
            'edit' => EditTypeDossier::route('/{record}/edit'),
        ];
    }
        public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
