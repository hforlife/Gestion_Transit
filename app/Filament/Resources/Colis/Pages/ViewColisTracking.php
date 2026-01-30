<?php

namespace App\Filament\Resources\Colis\Pages;

use App\Filament\Resources\Colis\ColisResource;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

class ViewColisTracking extends Page
{
    protected static string $resource = ColisResource::class;

    public $record;

    public function mount($record): void
    {
        $this->record = ColisResource::getModel()::findOrFail($record);
    }

    /** ✅ DOIT ÊTRE PUBLIC */
    // public function getSchema(): Schema
    // {
    //     return Schema::make()
    //         ->components([
    //             Section::make('Informations du colis')
    //                 ->schema([
    //                     Text::make()
    //                         ->content("BL : {$this->record->numero_bl}"),

    //                     Text::make()
    //                         ->content("État : {$this->record->etat_colis}"),
    //                 ])
    //                 ->columns(2),

    //             Section::make('Timeline du colis')
    //                 ->schema([
    //                     Repeater::make('timeline')
    //                         ->state($this->record->getTimeline())
    //                         ->schema([
    //                             TextInput::make('label')->disabled(),
    //                             TextInput::make('date')->disabled(),
    //                             TextInput::make('status')->disabled(),
    //                         ])
    //                         ->columns(3)
    //                         ->disabled()
    //                         ->dehydrated(false),
    //                 ]),
    //         ]);
    // }
}
