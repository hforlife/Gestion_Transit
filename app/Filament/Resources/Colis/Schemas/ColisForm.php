<?php

namespace App\Filament\Resources\Colis\Schemas;

use App\Models\TypeColis;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;

class ColisForm
{
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
                        ->afterValidation(function ($livewire) {

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
                    }),

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 2 : PORT
                    |--------------------------------------------------------------------------
                    */
                    Step::make('Port')
                        ->visible(fn () =>
                            auth()->user()?->hasRole(['super_admin', 'agent_portuaire', 'supervisor'])
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
                        ->afterValidation(function ($livewire) {

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
                    }),

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 3 : DOUANE
                    |--------------------------------------------------------------------------
                    */
                    Step::make('Douane')
                        ->visible(fn () =>
                            auth()->user()?->hasRole(['super_admin', 'agent_douanier', 'supervisor'])
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
                        ->afterValidation(function ($livewire) {

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
                    }),

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 4 : EXPERTISE
                    |--------------------------------------------------------------------------
                    */
                    Step::make('Expertise')
                        ->visible(fn ($get) =>
                            $get('type_vehicule') === true ||
                            auth()->user()?->hasRole(['super_admin', 'expert_ont', 'supervisor'])
                        )
                        ->schema([
                            Grid::make(2)->schema([

                                Select::make('etat_expertise')
                                    ->options([
                                        'EN_ATTENTE' => 'En attente',
                                        'EFFECTUEE' => 'Effectuée',
                                    ])
                                    ->default('EN_ATTENTE'),

                                TextInput::make('num_pvc'),

                                Select::make('etat_pvc')
                                    ->options([
                                        'NON_RECU' => 'Non reçu',
                                        'RECU' => 'Reçu',
                                        'PAYE' => 'Payé',
                                    ])
                                    ->default('NON_RECU'),
                            ]),
                        ])
                        ->afterValidation(function ($livewire) {

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
                    }),

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 5 : FINALISATION
                    |--------------------------------------------------------------------------
                    */
                    Step::make('Finalisation')
                        ->visible(fn () =>
                            auth()->user()?->hasRole(['super_admin', 'agent_logistique', 'supervisor'])
                        )
                        ->schema([
                            Grid::make(2)->schema([

                                Select::make('status')
                                    ->options([
                                        'EN_COURS' => 'En cours',
                                        'TERMINE' => 'Terminé',
                                    ])
                                    ->default('EN_COURS'),

                                DatePicker::make('date_livraison')
                                    ->native(false),

                                Textarea::make('commentaires_cloture')
                                    ->columnSpanFull(),
                            ]),
                        ])
                        ->afterValidation(function ($livewire) {

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
                    }),
                ])
               

                ->persistStepInQueryString()
                ->columnSpanFull()
                ->columns(1),
            ]);
    }
}
