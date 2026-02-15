<?php

namespace App\Filament\Resources\DossierTransits;

use App\Filament\Resources\DossierTransits\Pages\CreateDossierTransit;
use App\Filament\Resources\DossierTransits\Pages\EditDossierTransit;
use App\Filament\Resources\DossierTransits\Pages\ListDossierTransits;
use App\Filament\Resources\DossierTransits\Schemas\DossierTransitForm;
use App\Filament\Resources\DossierTransits\Tables\DossierTransitsTable;
use App\Models\DossierTransit;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DossierTransitResource extends Resource
{
    protected static ?string $model = DossierTransit::class;

    protected static string | UnitEnum | null $navigationGroup = 'Archives';

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'App/Models/DossierTransit';

    public static function form(Schema $schema): Schema
    {
        return DossierTransitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DossierTransitsTable::configure($table);
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
            'index' => ListDossierTransits::route('/'),
            'create' => CreateDossierTransit::route('/create'),
            'edit' => EditDossierTransit::route('/{record}/edit'),
        ];
    }
        public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
