<?php

namespace App\Filament\Resources\Colis\Schemas;

use App\Models\TypeColis;
use Filament\Schemas\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Html;

class ColisForm
{
    protected static function saveRecord($livewire): void
    {
        $data = $livewire->form->getState();

        // 🔥 crée le record si pas encore créé
        if (!$livewire->record) {
            $livewire->record = $livewire->getModel()::create(
                $livewire->form->getState()
            );
        } else {

            // 🔥 update sinon
            $livewire->record->update(
                $livewire->form->getState()
            );
        }
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 1 : ENREGISTREMENT
                    |--------------------------------------------------------------------------
                    */
                    Step::make('Enregistrement')
                        ->visible(
                            fn() =>
                            auth()->user()?->hasRole(['super_admin', 'agent_saisie'])
                        )
                        ->schema([
                            Section::make('Informations du colis')
                                ->schema([
                                    Grid::make(2)->schema([

                                        TextInput::make('numero_bl')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(100),

                                        Select::make('id_type_colis')
                                            ->relationship('typeColis', 'nom')
                                            ->required()
                                            ->preload()
                                            ->searchable()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $type = TypeColis::find($state);
                                                    if ($type && str_contains(strtolower($type->nom), 'chassis')) {
                                                        $set('type_vehicule', true);
                                                    }
                                                }
                                            }),

                                        Hidden::make('type_vehicule')
                                            ->default(false),

                                        TextInput::make('description')
                                            ->columnSpanFull(),

                                        Select::make('client_id')
                                            ->relationship('client', 'nom')
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        Select::make('id_port')
                                            ->relationship('port', 'nom')
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        Select::make('user_id')
                                            ->relationship('agent', 'name')
                                            ->required()
                                            ->default(auth()->id()),
                                    ]),
                                ]),
                        ])
                        ->afterValidation(fn($livewire) => self::saveRecord($livewire)),

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 2 : PORT
                    |--------------------------------------------------------------------------
                    */
                    Step::make('Port')
                        ->visible(
                            fn() =>
                            auth()->user()?->hasRole(['super_admin', 'agent_transit'])
                        )
                        ->schema([
                            Grid::make(2)->schema([

                                Select::make('status_colis_port')
                                    ->options([
                                        'EN_ATTENTE' => 'En attente',
                                        'ENTRE' => 'Entré',
                                        'SORTI' => 'Sorti',
                                    ])
                                    ->default('EN_ATTENTE'),

                                DatePicker::make('date_entree_port')
                                    ->native(false),

                                DatePicker::make('date_sortie_port')
                                    ->afterOrEqual('date_entree_port')
                                    ->native(false),
                            ]),
                        ])
                        ->afterValidation(fn($livewire) => self::saveRecord($livewire)),

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 3 : DOUANE
                    |--------------------------------------------------------------------------
                    */
                    Step::make('Douane')
                        ->visible(
                            fn() =>
                            auth()->user()?->hasRole(['super_admin', 'agent_transit'])
                        )
                        ->schema([
                            Grid::make(2)->schema([

                                TextInput::make('num_t1'),

                                Select::make('etat_t1')
                                    ->options([
                                        'FOURNI' => 'Fourni',
                                        'PAYE' => 'Payé',
                                    ]),

                                TextInput::make('declaration_reference')
                                    ->columnSpanFull(),

                                Select::make('status_colis_douane')
                                    ->options([
                                        'EN_ATTENTE' => 'En attente',
                                        'ENTRE' => 'Entré',
                                        'SORTI' => 'Sorti',
                                    ])
                                    ->default('EN_ATTENTE'),

                                DatePicker::make('date_entree_douane')
                                    ->native(false),

                                DatePicker::make('date_sortie_douane')
                                    ->afterOrEqual('date_entree_douane')
                                    ->native(false),
                            ]),
                        ])
                        ->afterValidation(fn($livewire) => self::saveRecord($livewire)),

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 4 : EXPERTISE
                    |--------------------------------------------------------------------------
                    */
                    Step::make('Expertise')

                        ->visible(function ($get, $livewire) {

                            $typeId = $get('id_type_colis')
                                ?? $livewire->record?->id_type_colis;

                            if (!$typeId) {
                                return false;
                            }

                            $type = TypeColis::find($typeId);

                            $isVehicule = $type
                                && strcasecmp($type->nom, 'Véhicules') === 0;

                            return $isVehicule
                                || auth()->user()?->hasRole('super_admin');
                        })

                        ->schema(function ($get, $livewire) {

                            $typeId = $get('id_type_colis')
                                ?? $livewire->record?->id_type_colis;

                            $type = $typeId ? TypeColis::find($typeId) : null;

                            $isVehicule = $type
                                && strcasecmp($type->nom, 'Véhicules') === 0;

                            /*
                            |--------------------------------------------------------------------------
                            | SI PAS VEHICULE → MESSAGE
                            |--------------------------------------------------------------------------
                            */
                            if (!$isVehicule) {

                                return [

                                    Section::make('Expertise non requise')
                                        ->icon('heroicon-o-information-circle')
                                        ->schema([

                                            Html::make('info_expertise')
                                                ->content(new HtmlString(
                                                    '<div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h3 class="text-sm font-semibold text-blue-800">
                        Type de colis : ' . e($type?->nom ?? 'Conteneur') . '
                    </h3>
                    <p class="text-sm text-blue-700 mt-2">
                        L\'expertise ONT est uniquement requise pour les colis 
                        de type <strong>Véhicules</strong>.
                        Ce colis ne nécessite donc pas d\'expertise.
                    </p>
                </div>'
                                                ))
                                                ->columnSpanFull(),

                                        ])
                                        ->columnSpanFull(),

                                ];
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | SI VEHICULE → FORMULAIRE
                            |--------------------------------------------------------------------------
                            */

                            return [

                                Grid::make(2)->schema([

                                    Select::make('etat_expertise')
                                        ->label('État de l\'expertise')
                                        ->options([
                                            'EN_ATTENTE' => 'En attente',
                                            'EFFECTUEE' => 'Effectuée',
                                        ])
                                        ->default('EN_ATTENTE')
                                        ->live()
                                        ->columnSpanFull(),

                                    Section::make('Procès-Verbal de Contrôle (PVC)')
                                        ->compact()
                                        ->schema([
                                            Grid::make(2)->schema([

                                                TextInput::make('num_pvc')
                                                    ->label('Numéro PVC')
                                                    ->maxLength(50)
                                                    ->required(
                                                        fn($get) =>
                                                        $get('etat_expertise') === 'EFFECTUEE'
                                                    ),

                                                Select::make('etat_pvc')
                                                    ->options([
                                                        'NON_RECU' => 'Non reçu',
                                                        'RECU' => 'Reçu',
                                                        'PAYE' => 'Payé',
                                                    ])
                                                    ->default('NON_RECU'),

                                            ]),
                                        ])
                                        ->columnSpanFull(),

                                    Section::make('Autorisation d\'Enlèvement (AE)')
                                        ->compact()
                                        ->schema([
                                            Grid::make(2)->schema([

                                                TextInput::make('num_ae')
                                                    ->maxLength(50),

                                                Select::make('etat_ae')
                                                    ->options([
                                                        'NON_VALIDE' => 'Non valide',
                                                        'VALIDE' => 'Valide',
                                                    ])
                                                    ->default('NON_VALIDE'),

                                            ]),
                                        ])
                                        ->columnSpanFull(),

                                    Section::make('Certificat de Mise en Conformité (CMC)')
                                        ->compact()
                                        ->schema([
                                            Grid::make(2)->schema([

                                                TextInput::make('num_cmc')
                                                    ->maxLength(50),

                                                Select::make('etat_cmc')
                                                    ->options([
                                                        'NON_RECU' => 'Non reçu',
                                                        'RECU' => 'Reçu',
                                                    ])
                                                    ->default('NON_RECU'),

                                            ]),
                                        ])
                                        ->columnSpanFull(),

                                ]),

                            ];
                        })

                        ->afterValidation(fn($livewire) => self::saveRecord($livewire)),

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 5 : LIVRAISON
                    |--------------------------------------------------------------------------
                    */
                    Step::make('Livraison')
                        ->visible(
                            fn() =>
                            auth()->user()?->hasRole(['super_admin', 'agent_saisie'])
                        )
                        ->schema([
                            Grid::make(2)->schema([

                                Select::make('status_colis_livraison')
                                    ->options([
                                        'EN_ATTENTE' => 'En attente',
                                        'LIVRE' => 'Livré',
                                    ])
                                    ->default('EN_ATTENTE'),

                                DatePicker::make('date_livraison')
                                    ->native(false),
                            ]),
                        ])
                        ->afterValidation(fn($livewire) => self::saveRecord($livewire)),

                    /*
                |--------------------------------------------------------------------------
                | STEP 6 : CLOTURE
                |--------------------------------------------------------------------------
                */
                    Step::make('Finalisation')
                        ->visible(
                            fn() =>
                            auth()->user()?->hasRole(['super_admin', 'agent_saisie'])
                        )
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('status')
                                    ->options([
                                        'EN_COURS' => 'En cours',
                                        'TERMINE' => 'Terminé',
                                    ])
                                    ->default('EN_COURS'),
                                Textarea::make('commentaires_cloture')
                                    ->columnSpanFull(),
                            ]),
                        ])
                        ->afterValidation(fn($livewire) => self::saveRecord($livewire)),
                ])


                    ->persistStepInQueryString()
                    ->columnSpanFull()
                    ->columns(1),
            ]);
    }
}
