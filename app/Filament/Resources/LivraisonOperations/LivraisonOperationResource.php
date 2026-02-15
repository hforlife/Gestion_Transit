<?php

namespace App\Filament\Resources\LivraisonOperations;

use App\Filament\Resources\LivraisonOperations\Pages\CreateLivraisonOperation;
use App\Filament\Resources\LivraisonOperations\Pages\EditLivraisonOperation;
use App\Filament\Resources\LivraisonOperations\Pages\ListLivraisonOperations;
use App\Filament\Resources\LivraisonOperations\Schemas\LivraisonOperationForm;
use App\Filament\Resources\LivraisonOperations\Tables\LivraisonOperationsTable;
use App\Models\LivraisonOperation;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LivraisonOperationResource extends Resource
{
    protected static ?string $model = LivraisonOperation::class;

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string | UnitEnum | null $navigationGroup = 'Opérations';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'App\Models\LivraisonOperation';

    public static function form(Schema $schema): Schema
    {
        return LivraisonOperationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LivraisonOperationsTable::configure($table);
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
            'index' => ListLivraisonOperations::route('/'),
            'create' => CreateLivraisonOperation::route('/create'),
            'edit' => EditLivraisonOperation::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
