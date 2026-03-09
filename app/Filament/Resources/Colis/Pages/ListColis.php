<?php

namespace App\Filament\Resources\Colis\Pages;

use App\Filament\Resources\Colis\ColisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HasExports;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;

class ListColis extends ListRecords
{
    use HasExports;

    protected static string $resource = ColisResource::class;

    /**
     * Actions du header (ex: bouton créer)
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Widgets du header
     */
    protected function getHeaderWidgets(): array
    {
        return ColisResource::getWidgets();
    }

    /**
     * Onglets filtrant les colis
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous'),

            'conteneur' => Tab::make('Conteneurs')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereHas(
                        'typeColis',
                        fn($q) => $q->where('nom', 'Conteneur')
                    )
                ),

            'chassis' => Tab::make('Châssis')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereHas(
                        'typeColis',
                        fn($q) => $q->where('nom', 'Chassis')
                    )
                ),
        ];
    }

    /**
     * Actions par ligne dans le tableau
     */
    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                // Voir détails
                Action::make('voir')
                    ->label('Détails complets')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn($record) => ColisResource::getUrl('view', ['record' => $record])),

                // Récapitulatif complet
                $this->getPrintAction('pdf.recap-complet', 'Récapitulatif complet'),

                // Edit
                EditAction::make()
                    ->label('Modifier')
                    ->visible(fn($record) => $record->status !== 'TERMINE')
                    ->url(fn($record) => ColisResource::getUrl('edit', [
                        'record' => $record,
                        'step' => match ($record->etat_colis) {
                            'BL_ENREGISTRE' => 'Enregistrement',
                            'A_LA_DOUANE' => 'Port',
                            'EN_ROUTE' => 'Douane',
                            'LIVRE' => 'Livraison',
                            'CLOTURE' => 'Finalisation',
                            default => 'Enregistrement',
                        }
                    ])),

                // Delete
                DeleteAction::make()
                    ->label('Supprimer')
                    ->visible(fn($record) => $record->status !== 'TERMINE')
            ]),
        ];
    }
}
