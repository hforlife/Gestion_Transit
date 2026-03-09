<?php

namespace App\Filament\Resources\ColisUnites;

use App\Filament\Resources\ColisUnites\Pages\CreateColisUnite;
use App\Filament\Resources\ColisUnites\Pages\EditColisUnite;
use App\Filament\Resources\ColisUnites\Pages\ListColisUnites;
use App\Filament\Resources\ColisUnites\Schemas\ColisUniteForm;
use App\Filament\Resources\ColisUnites\Tables\ColisUnitesTable;
use App\Models\ColisUnite;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ColisUniteResource extends Resource
{
    protected static ?string $model = ColisUnite::class;

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | UnitEnum | null $navigationGroup = 'Colis / BL';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'App\Models\ColisUnite';

    public static function form(Schema $schema): Schema
    {
        return ColisUniteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ColisUnitesTable::configure($table);
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
            'index' => ListColisUnites::route('/'),
            'create' => CreateColisUnite::route('/create'),
            'edit' => EditColisUnite::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['colis']);
    }
}
