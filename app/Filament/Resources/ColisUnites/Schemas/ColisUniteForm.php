<?php

namespace App\Filament\Resources\ColisUnites\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

class ColisUniteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Informations de base (toujours visibles)
                Grid::make(2)->schema([

                    // Colis
                    Select::make('colis_id')
                        ->label('Colis/BL')
                        ->relationship('colis', 'numero_bl')
                        ->searchable()
                        ->preload()
                        ->required(),

                    // Type d'unité
                    Select::make('type')
                        ->label('Type d\'unité')
                        ->options([
                            'CONTENEUR' => 'Conteneur',
                            'CHASSIS' => 'Châssis',
                            'CHASSIS_VOITURE' => 'Châssis Voiture',
                            'CHASSIS_MACHINE' => 'Châssis Machine',
                        ])
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            // Reset des champs spécifiques selon le type
                            if ($state === 'CONTENEUR') {
                                $set('numero_chassis', null);
                                $set('vin', null);
                            } else {
                                $set('numero_conteneur', null);
                            }
                        }),

                    // État actuel
                    Select::make('etat')
                        ->label('État actuel')
                        ->options([
                            'AU_PORT' => 'Au port',
                            'A_LA_DOUANE' => 'En douane',
                            'EXPERTISE' => 'En expertise',
                            'EN_ROUTE' => 'En route',
                            'LIVRE' => 'Livré',
                        ])
                        ->default('AU_PORT')
                        ->required()
                        ->live(),

                    // Numéro de conteneur (visible uniquement pour CONTENEUR)
                    TextInput::make('numero_conteneur')
                        ->label('Numéro conteneur')
                        ->placeholder('Ex: MSCU1234567')
                        ->visible(fn ($get) => $get('type') === 'CONTENEUR')
                        ->required(fn ($get) => $get('type') === 'CONTENEUR')
                        ->maxLength(20),

                    // Numéro de châssis (visible pour tous types de châssis)
                    TextInput::make('numero_chassis')
                        ->label('Numéro châssis')
                        ->placeholder('Ex: CH-2026-001')
                        ->visible(fn ($get) => in_array($get('type'), [
                            'CHASSIS',
                            'CHASSIS_VOITURE',
                            'CHASSIS_MACHINE'
                        ]))
                        ->required(fn ($get) => in_array($get('type'), [
                            'CHASSIS',
                            'CHASSIS_VOITURE',
                            'CHASSIS_MACHINE'
                        ]))
                        ->maxLength(30),

                    // VIN (visible pour voitures et machines)
                    TextInput::make('vin')
                        ->label('Numéro VIN')
                        ->placeholder('17 caractères alphanumériques')
                        ->visible(fn ($get) => in_array($get('type'), [
                            'CHASSIS_VOITURE',
                            'CHASSIS_MACHINE'
                        ]))
                        ->required(fn ($get) => in_array($get('type'), [
                            'CHASSIS_VOITURE',
                            'CHASSIS_MACHINE'
                        ]))
                        ->maxLength(17)
                        ->helperText('Format standard 17 caractères'),
                ]),

                // Onglets pour les différentes étapes
                Tabs::make('Suivi de l\'unité')
                    ->tabs([

                        /* =======================
                           PORT
                        ======================= */
                        Tab::make('Port')
                            // ->icon('heroicon-o-anchor')
                            ->schema([
                                Section::make('Opérations portuaires')
                                    ->schema([
                                        Grid::make(2)->schema([

                                            Select::make('status_port')
                                                ->label('Statut au port')
                                                ->options([
                                                    'EN_ATTENTE' => 'En attente',
                                                    'AU_PORT' => 'Au port',
                                                    'SORTI' => 'Sorti',
                                                ])
                                                ->default('EN_ATTENTE')
                                                ->live(),

                                            DatePicker::make('date_arrivee_port')
                                                ->label('Date d\'arrivée')
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->required(fn ($get) => in_array($get('status_port'), ['AU_PORT', 'SORTI'])),

                                            DatePicker::make('date_sortie_port')
                                                ->label('Date de sortie')
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->afterOrEqual('date_arrivee_port')
                                                ->required(fn ($get) => $get('status_port') === 'SORTI'),
                                        ]),
                                    ]),
                            ]),

                        /* =======================
                           DOUANE
                        ======================= */
                        Tab::make('Douane')
                            ->icon('heroicon-o-document-magnifying-glass')
                            ->schema([
                                Section::make('Formalités douanières')
                                    ->schema([
                                        Grid::make(2)->schema([

                                            TextInput::make('num_t1')
                                                ->label('Numéro T1')
                                                ->placeholder('Ex: T1-2026-001234')
                                                ->maxLength(50),

                                            Select::make('etat_t1')
                                                ->label('État T1')
                                                ->options([
                                                    'NON_FOURNI' => 'Non fourni',
                                                    'FOURNI' => 'Fourni',
                                                    'PAYE' => 'Payé',
                                                ])
                                                ->default('NON_FOURNI'),

                                            TextInput::make('declaration_reference')
                                                ->label('Référence déclaration')
                                                ->placeholder('Ex: DEC-2026-001234')
                                                ->columnSpanFull()
                                                ->maxLength(100),

                                            Select::make('status_douane')
                                                ->label('Statut en douane')
                                                ->options([
                                                    'EN_ATTENTE' => 'En attente',
                                                    'ENTRE' => 'Entré',
                                                    'SORTI' => 'Sorti',
                                                ])
                                                ->default('EN_ATTENTE')
                                                ->live(),

                                            DatePicker::make('date_entree_douane')
                                                ->label('Date d\'entrée')
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->required(fn ($get) => in_array($get('status_douane'), ['ENTRE', 'SORTI'])),

                                            DatePicker::make('date_sortie_douane')
                                                ->label('Date de sortie')
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->afterOrEqual('date_entree_douane')
                                                ->required(fn ($get) => $get('status_douane') === 'SORTI'),
                                        ]),
                                    ]),
                            ]),

                        /* =======================
                           EXPERTISE
                        ======================= */
                        Tab::make('Expertise')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->visible(fn ($get) => in_array($get('type'), [
                                'CHASSIS',
                                'CHASSIS_VOITURE',
                                'CHASSIS_MACHINE'
                            ]))
                            ->schema([
                                Section::make('Expertise ONT')
                                    ->schema([
                                        Grid::make(2)->schema([

                                            Select::make('etat_expertise')
                                                ->label('État expertise')
                                                ->options([
                                                    'EN_ATTENTE' => 'En attente',
                                                    'EFFECTUEE' => 'Effectuée',
                                                ])
                                                ->default('EN_ATTENTE')
                                                ->live()
                                                ->columnSpanFull(),

                                            // PVC
                                            Section::make('Procès-Verbal de Contrôle (PVC)')
                                                ->compact()
                                                ->schema([
                                                    Grid::make(2)->schema([
                                                        TextInput::make('num_pvc')
                                                            ->label('Numéro PVC')
                                                            ->placeholder('Ex: PVC-2026-001')
                                                            ->maxLength(50)
                                                            ->required(fn ($get) => $get('etat_expertise') === 'EFFECTUEE'),

                                                        Select::make('etat_pvc')
                                                            ->label('État PVC')
                                                            ->options([
                                                                'NON_RECU' => 'Non reçu',
                                                                'RECU' => 'Reçu',
                                                                'PAYE' => 'Payé',
                                                            ])
                                                            ->default('NON_RECU'),
                                                    ]),
                                                ]),

                                            // AE
                                            Section::make('Autorisation d\'Enlèvement (AE)')
                                                ->compact()
                                                ->schema([
                                                    Grid::make(2)->schema([
                                                        TextInput::make('num_ae')
                                                            ->label('Numéro AE')
                                                            ->placeholder('Ex: AE-2026-001')
                                                            ->maxLength(50)
                                                            ->required(fn ($get) => $get('etat_expertise') === 'EFFECTUEE'),

                                                        Select::make('etat_ae')
                                                            ->label('État AE')
                                                            ->options([
                                                                'NON_VALIDE' => 'Non valide',
                                                                'VALIDE' => 'Valide',
                                                            ])
                                                            ->default('NON_VALIDE'),
                                                    ]),
                                                ]),

                                            // CMC
                                            Section::make('Certificat de Mise en Conformité (CMC)')
                                                ->compact()
                                                ->schema([
                                                    Grid::make(2)->schema([
                                                        TextInput::make('num_cmc')
                                                            ->label('Numéro CMC')
                                                            ->placeholder('Ex: CMC-2026-001')
                                                            ->maxLength(50)
                                                            ->required(fn ($get) => $get('etat_expertise') === 'EFFECTUEE'),

                                                        Select::make('etat_cmc')
                                                            ->label('État CMC')
                                                            ->options([
                                                                'NON_RECU' => 'Non reçu',
                                                                'RECU' => 'Reçu',
                                                            ])
                                                            ->default('NON_RECU'),
                                                    ]),
                                                ]),
                                        ]),
                                    ]),
                            ]),

                        /* =======================
                           LIVRAISON
                        ======================= */
                        Tab::make('Livraison')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Section::make('Finalisation')
                                    ->schema([
                                        Grid::make(2)->schema([

                                            DatePicker::make('date_livraison')
                                                ->label('Date de livraison')
                                                ->native(false)
                                                ->displayFormat('d/m/Y H:i')
                                                ->seconds(false)
                                                ->required(fn ($get) => $get('etat') === 'LIVRE'),

                                            Placeholder::make('info_livraison')
                                                ->label('')
                                                // ->placeholder('Informations complémentaires sur la livraison')
                                                ->content(new HtmlString(
                                                    '<div class="text-sm text-gray-500">
                                                        La livraison marque la fin du parcours de cette unité.
                                                    </div>'
                                                ))
                                                ->columnSpanFull(),
                                        ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                // Champ caché pour la relation
                // Hidden::make('colis_id'),
            ])
            ->columns(1);
    }
}
