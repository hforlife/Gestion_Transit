<?php

namespace App\Filament\Pages;

use App\Livewire\RapportGeneralOverview;
use App\Models\Port;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class RapportGeneral extends Page
{
    use HasFiltersForm; //HasPageShield;

    // protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Rapport Général';
    protected static string | UnitEnum | null $navigationGroup = 'Rapports';
    protected static ?int $navigationSort = 12;

    protected  string $view = 'filament.pages.rapport-general';

    public function getTitle(): string
    {
        return 'Rapport Général des Opérations de Transit';
    }

    public function getSubheading(): ?string
    {
        return 'Statistiques détaillées des dossiers et colis par période';
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Filtres')
                    ->schema([
                        // Période
                        Select::make('periode')
                            ->label('Période')
                            ->options([
                                '' => 'Personnalisée',
                                'aujourd_hui' => 'Aujourd\'hui',
                                'semaine' => 'Cette semaine',
                                'mois' => 'Ce mois',
                                'trimestre' => 'Ce trimestre',
                                'annee' => 'Cette année',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Auto-set les dates selon la période choisie
                                $dates = $this->getDatesForPeriode($state);
                                if ($dates) {
                                    $set('date_debut', $dates['debut']);
                                    $set('date_fin', $dates['fin']);
                                }
                            })
                            ->columnSpan(2),

                        // Dates personnalisées
                        DatePicker::make('date_debut')
                            ->label('Date de début')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->reactive()
                            ->columnSpan(1),

                        DatePicker::make('date_fin')
                            ->label('Date de fin')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->minDate(fn ($get) => $get('date_debut') ?: null)
                            ->reactive()
                            ->columnSpan(1),

                        // Filtres additionnels
                        Select::make('port_id')
                            ->label('Port')
                            ->options(\App\Models\Port::pluck('nom', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Tous les ports')
                            ->reactive()
                            ->columnSpan(2),

                        Select::make('type_dossier_id')
                            ->label('Type de dossier')
                            ->options(\App\Models\TypeDossier::pluck('nom', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Tous les types')
                            ->reactive()
                            ->columnSpan(2),

                        Select::make('type_colis_id')
                            ->label('Type de colis')
                            ->options(\App\Models\TypeColis::pluck('nom', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Tous les types')
                            ->reactive()
                            ->columnSpan(2),
                    ])
                    ->columns(8)
            ])
            ->statePath('filters');
    }

    protected function getDatesForPeriode($periode): ?array
    {
        return match($periode) {
            'aujourd_hui' => [
                'debut' => now()->startOfDay(),
                'fin' => now()->endOfDay(),
            ],
            'semaine' => [
                'debut' => now()->startOfWeek(),
                'fin' => now()->endOfWeek(),
            ],
            'mois' => [
                'debut' => now()->startOfMonth(),
                'fin' => now()->endOfMonth(),
            ],
            'trimestre' => [
                'debut' => now()->startOfQuarter(),
                'fin' => now()->endOfQuarter(),
            ],
            'annee' => [
                'debut' => now()->startOfYear(),
                'fin' => now()->endOfYear(),
            ],
            default => null,
        };
    }

    public function getFooterWidgets(): array
    {
        return [
            RapportGeneralOverview::class,
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('reset_filters')
                ->label('Réinitialiser les filtres')
                ->color('gray')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $this->filtersForm->fill([
                        'periode' => '',
                        'date_debut' => null,
                        'date_fin' => null,
                        'port_id' => null,
                        'type_dossier_id' => null,
                        'type_colis_id' => null,
                    ]);

                    $this->dispatch('filters-reset');
                }),

            \Filament\Actions\Action::make('exporter_pdf')
                ->label('Exporter PDF')
                ->color('danger')
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn () => $this->hasActiveFilters())
                ->action('exportPDF'),

            \Filament\Actions\Action::make('exporter_excel')
                ->label('Exporter Excel')
                ->color('success')
                ->icon('heroicon-o-table-cells')
                ->visible(fn () => $this->hasActiveFilters())
                ->action('exportExcel'),
        ];
    }

    protected function hasActiveFilters(): bool
    {
        $filters = $this->pageFilters ?? [];

        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                return true;
            }
        }

        return false;
    }
}
