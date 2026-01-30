<?php

namespace App\Filament\Resources\DouaneOperations;

use App\Filament\Resources\DouaneOperations\Pages\CreateDouaneOperation;
use App\Filament\Resources\DouaneOperations\Pages\EditDouaneOperation;
use App\Filament\Resources\DouaneOperations\Pages\ListDouaneOperations;
use App\Filament\Resources\DouaneOperations\Schemas\DouaneOperationForm;
use App\Filament\Resources\DouaneOperations\Tables\DouaneOperationsTable;
use App\Models\DouaneOperation;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DouaneOperationResource extends Resource
{
    protected static ?string $model = DouaneOperation::class;

    protected static string | UnitEnum | null $navigationGroup = 'Opérations';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice;

    protected static ?string $recordTitleAttribute = 'App/Models/DouaneOperation';

    public static function form(Schema $schema): Schema
    {
        return DouaneOperationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DouaneOperationsTable::configure($table);
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
            'index' => ListDouaneOperations::route('/'),
            'create' => CreateDouaneOperation::route('/create'),
            'edit' => EditDouaneOperation::route('/{record}/edit'),
        ];
    }
}
