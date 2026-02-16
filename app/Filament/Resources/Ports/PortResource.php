<?php

namespace App\Filament\Resources\Ports;

use App\Filament\Resources\Ports\Pages\CreatePort;
use App\Filament\Resources\Ports\Pages\EditPort;
use App\Filament\Resources\Ports\Pages\ListPorts;
use App\Filament\Resources\Ports\Schemas\PortForm;
use App\Filament\Resources\Ports\Tables\PortsTable;
use App\Models\Port;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PortResource extends Resource
{
    protected static ?string $model = Port::class;

    protected static string | UnitEnum | null $navigationGroup = 'Paramètres';

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'App/Models/Port';

    public static function form(Schema $schema): Schema
    {
        return PortForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PortsTable::configure($table);
    }

    public static function getNavigationLabel(): string
    {
        return 'Gestion des ports';
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
            'index' => ListPorts::route('/'),
            'create' => CreatePort::route('/create'),
            'edit' => EditPort::route('/{record}/edit'),
        ];
    }
        public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
