<?php

namespace App\Filament\Resources\PortOperations;

use App\Filament\Resources\PortOperations\Pages\CreatePortOperation;
use App\Filament\Resources\PortOperations\Pages\EditPortOperation;
use App\Filament\Resources\PortOperations\Pages\ListPortOperations;
use App\Filament\Resources\PortOperations\Schemas\PortOperationForm;
use App\Filament\Resources\PortOperations\Tables\PortOperationsTable;
use App\Models\PortOperation;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PortOperationResource extends Resource
{
    protected static ?string $model = PortOperation::class;

    protected static string | UnitEnum | null $navigationGroup = 'Opérations';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Truck;

    protected static ?string $recordTitleAttribute = 'App/Models/PortOPeration';

    public static function form(Schema $schema): Schema
    {
        return PortOperationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PortOperationsTable::configure($table);
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
            'index' => ListPortOperations::route('/'),
            'create' => CreatePortOperation::route('/create'),
            'edit' => EditPortOperation::route('/{record}/edit'),
        ];
    }
}
