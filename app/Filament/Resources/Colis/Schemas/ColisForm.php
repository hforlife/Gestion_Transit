<?php

namespace App\Filament\Resources\Colis\Schemas;

use App\Models\TypeColis;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;

use Filament\Forms\Set;
use Filament\Notifications\Notification;

use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;

class ColisForm
{
    /* =====================================================
     | SAVE RECORD
     ===================================================== */
protected static function saveRecord($livewire): void
{
    $data = $livewire->form->getState();

    // 🔥 Forcer les valeurs si absentes
    $data['etat_pvc'] = $data['etat_pvc'] ?? 'NON_RECU';
    $data['etat_ae'] = $data['etat_ae'] ?? 'NON_VALIDE';
    $data['etat_cmc'] = $data['etat_cmc'] ?? 'NON_RECU';
    $data['etat_expertise'] = $data['etat_expertise'] ?? 'EN_ATTENTE';

    if (!$livewire->record) {
        $livewire->record = $livewire->getModel()::create([
            ...$data,
            'etat_colis' => 'BL_ENREGISTRE',
            'status_colis_port' => 'EN_ATTENTE',
            'status_colis_douane' => 'EN_ATTENTE',
            'status_colis_livraison' => 'EN_ATTENTE',
            'status' => 'EN_COURS',
        ]);
    } else {
        $livewire->record->update($data);
    }
}


    /* =====================================================
     | COMPLETE STEP
     ===================================================== */
    protected static function completeStep(string $step, $record): void
    {
        if (!$record) return;

        $updates = match ($step) {
            'Port' => [
                'etat_colis' => 'A_LA_DOUANE',
                'date_sortie_port' => now(),
            ],
            'Douane' => [
                'etat_colis' => 'EN_ROUTE',
                'date_sortie_douane' => now(),
            ],
            'Livraison' => [
                'etat_colis' => 'LIVRE',
                'date_livraison' => now(),
            ],
            'Finalisation' => [
                'etat_colis' => 'CLOTURE',
                'status' => 'TERMINE',
            ],
            default => [],
        };

        $record->update($updates);
    }

    /* =====================================================
     | FORM CONFIGURATION
     ===================================================== */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([

                    /* =====================================================
                     | STEP 1 - ENREGISTREMENT
                     ===================================================== */
                    Step::make('Enregistrement')
                        ->visible(function ($get, $livewire) {

                            $user = auth()->user();

                            if (!$user) return false;

                            return (
                                $user->hasAnyRole([
                                    'super_admin',
                                    'agent_saisie'
                                ])
                            );
                        })
                        ->schema([
                            Section::make('Informations du colis')
                                ->schema([
                                    Grid::make(2)->schema([

                                        TextInput::make('numero_bl')
                                            ->required()
                                             ->rule(function ($livewire) {
                                                    return Rule::unique('colis', 'numero_bl')
                                                        ->ignore($livewire->record?->id);
                                                }),

                                        Select::make('id_type_colis')
                                            ->relationship('typeColis', 'nom')
                                            ->required()
                                            ->preload()
                                            ->searchable(),

                                        Textarea::make('description')
                                            ->columnSpanFull(),

                                        Select::make('id_dossier_transit')
                                            ->label('Dossier colis')
                                            ->relationship('dossierTransit', 'nom', function ($query, $livewire) {
                                                $record = $livewire->record;

                                                $query->where(function ($q) use ($record) {
                                                    $q->doesntHave('colis');

                                                    if ($record?->id_dossier_transit) {
                                                        $q->orWhere('id', $record->id_dossier_transit);
                                                    }
                                                });
                                            })
                                            ->required()
                                            ->preload()
                                            ->searchable(),

                                        Select::make('id_port')
                                            ->relationship('port', 'nom')
                                            ->required()
                                            ->preload()
                                            ->searchable(),

                                        Select::make('user_id')
                                            ->relationship('agent', 'name')
                                            ->default(auth()->id())
                                            ->preload()
                                            ->required(),
                                    ]),
                                ]),
                        ])
                        ->afterValidation(fn($livewire) => self::saveRecord($livewire)),

                    /* =====================================================
                     | STEP 2 - PORT
                     ===================================================== */
                    Step::make('Port')
                        ->visible(function ($get, $livewire) {

                            $user = auth()->user();

                            if (!$user) return false;

                            return (
                                $user->hasAnyRole([
                                    'super_admin',
                                    'agent_transit',
                                ])
                            );
                        })
                        ->schema([
                            Section::make('Opérations portuaires')
                                ->schema([
                                    Grid::make(2)->schema([

                                        Select::make('status_colis_port')
                                            ->options([
                                                'EN_ATTENTE' => 'En attente',
                                                'ENTRE' => 'Entré',
                                                'SORTI' => 'Sorti',
                                            ])
                                            ->default('EN_ATTENTE')
                                            ->live(),

                                        DatePicker::make('date_entree_port'),

                                        DatePicker::make('date_sortie_port')
                                            ->afterOrEqual('date_entree_port'),
                                    ]),

                                    Actions::make([
                                        Action::make('valider_port')
                                            ->label('Valider étape')
                                            ->icon('heroicon-o-check-circle')
                                            ->color('success')
                                            ->visible(fn($livewire) =>
                                                $livewire->record &&
                                                $livewire->record->status_colis_port === 'SORTI'
                                            )
                                            ->action(function ($livewire) {
                                                self::completeStep('Port', $livewire->record);

                                                Notification::make()
                                                    ->title('Étape Port validée')
                                                    ->success()
                                                    ->send();
                                            }),
                                    ]),
                                ]),
                        ])
                        ->afterValidation(fn($livewire) => self::saveRecord($livewire)),

                    /* =====================================================
                     | STEP 3 - DOUANE
                     ===================================================== */
                    Step::make('Douane')
                        ->visible(function ($get, $livewire) {

                            $user = auth()->user();

                            if (!$user) return false;

                            return (
                                $user->hasAnyRole([
                                    'super_admin',
                                    'agent_transit',
                                ])
                            );
                        })
                        ->schema([
                            Section::make('Formalités douanières')
                                ->schema([
                                    Grid::make(2)->schema([

                                        TextInput::make('num_t1'),

                                        Select::make('status_colis_douane')
                                            ->options([
                                                'EN_ATTENTE' => 'En attente',
                                                'ENTRE' => 'Entré',
                                                'SORTI' => 'Sorti',
                                            ])
                                            ->default('EN_ATTENTE')
                                            ->live(),

                                        DatePicker::make('date_entree_douane'),

                                        DatePicker::make('date_sortie_douane')
                                            ->afterOrEqual('date_entree_douane'),
                                    ]),

                                    Actions::make([
                                        Action::make('valider_douane')
                                            ->label('Valider étape')
                                            ->icon('heroicon-o-check-circle')
                                            ->color('success')
                                            ->visible(fn($livewire) =>
                                                $livewire->record &&
                                                $livewire->record->status_colis_douane === 'SORTI'
                                            )
                                            ->action(function ($livewire) {
                                                self::completeStep('Douane', $livewire->record);

                                                Notification::make()
                                                    ->title('Étape Douane validée')
                                                    ->success()
                                                    ->send();
                                            }),
                                    ]),
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

                            $user = auth()->user();

                            if (!$user) return false;

                            $typeId = $get('id_type_colis') ?? $livewire->record?->id_type_colis;
                            if (!$typeId) return false;

                            $type = TypeColis::find($typeId);
                            $isVehicule = $type && strcasecmp($type->nom, 'Véhicules') === 0;

                            return (
                                $user->hasAnyRole([
                                    'super_admin',
                                    'expert',
                                ])
                                && $isVehicule
                            );
                        })
                        ->schema(function ($get, $livewire) {
                            $typeId = $get('id_type_colis') ?? $livewire->record?->id_type_colis;
                            $type = $typeId ? TypeColis::find($typeId) : null;
                            $isVehicule = $type && strcasecmp($type->nom, 'Véhicules') === 0;

                            $user = auth()->user();
                            $canSeeMessage = $user?->hasAnyRole(['super-admin', 'expert']);

                            if (!$isVehicule && $canSeeMessage) {
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
                                                        </p>
                                                    </div>'
                                                ))
                                                ->columnSpanFull(),
                                        ])
                                        ->columnSpanFull(),
                                ];
                            }

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
                                        ])
                                        ->columnSpanFull(),

                                    Section::make('Autorisation d\'Enlèvement (AE)')
                                        ->compact()
                                        ->schema([
                                            Grid::make(2)->schema([
                                                TextInput::make('num_ae')
                                                    ->label('Numéro AE')
                                                    ->maxLength(50),

                                                Select::make('etat_ae')
                                                    ->label('État AE')
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
                                                    ->label('Numéro CMC')
                                                    ->maxLength(50),

                                                Select::make('etat_cmc')
                                                    ->label('État CMC')
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
                    /* =====================================================
                     | STEP 4 - LIVRAISON
                     ===================================================== */
                    Step::make('Livraison')
                                                               ->visible(function ($get, $livewire) {

                            $user = auth()->user();

                            if (!$user) return false;

                            return (
                                $user->hasAnyRole([
                                    'super_admin',
                                    'agent_transit',
                                ])
                            );
                        })
                        ->schema([
                            Section::make('Livraison')
                                ->schema([
                                    Grid::make(2)->schema([

                                        Select::make('status_colis_livraison')
                                            ->options([
                                                'EN_ATTENTE' => 'En attente',
                                                'LIVRE' => 'Livré',
                                            ])
                                            ->default('EN_ATTENTE')
                                            ->live(),

                                        DatePicker::make('date_livraison'),

                                        Textarea::make('commentaires_cloture')
                                            ->columnSpanFull(),
                                    ]),

                                    Actions::make([
                                        Action::make('valider_livraison')
                                            ->label('Valider livraison')
                                            ->color('success')
                                            ->visible(fn($livewire) =>
                                                $livewire->record &&
                                                $livewire->record->status_colis_livraison === 'LIVRE'
                                            )
                                            ->action(function ($livewire) {
                                                self::completeStep('Livraison', $livewire->record);

                                                Notification::make()
                                                    ->title('Livraison validée')
                                                    ->success()
                                                    ->send();
                                            }),
                                    ]),
                                ]),
                        ])
                        ->afterValidation(fn($livewire) => self::saveRecord($livewire)),

                    /* =====================================================
                     | STEP 5 - FINALISATION
                     ===================================================== */
                    Step::make('Finalisation')
                                                               ->visible(function ($get, $livewire) {

                            $user = auth()->user();

                            if (!$user) return false;

                            return (
                                $user->hasAnyRole([
                                    'super_admin',
                                    'agent_transit',
                                ])
                            );
                        })
                        ->schema([
                            Section::make('Clôture')
                                ->schema([
                                    Select::make('status')
                                        ->options([
                                            'EN_COURS' => 'En cours',
                                            'TERMINE' => 'Terminé',
                                        ])
                                        ->default('EN_COURS'),

                                    Actions::make([
                                        Action::make('cloturer')
                                            ->label('Clôturer dossier')
                                            ->color('success')
                                            ->visible(fn($livewire) =>
                                                $livewire->record &&
                                                $livewire->record->status === 'TERMINE'
                                            )
                                            ->action(function ($livewire) {
                                                self::completeStep('Finalisation', $livewire->record);

                                                Notification::make()
                                                    ->title('Dossier clôturé')
                                                    ->success()
                                                    ->send();

                                                return redirect(
                                                    \App\Filament\Resources\Colis\ColisResource::getUrl('index')
                                                );
                                            }),
                                    ]),
                                ]),
                        ])
                        ->afterValidation(fn($livewire) => self::saveRecord($livewire)),
                ])
                ->persistStepInQueryString('step')
                ->columnSpanFull(),
            ]);
    }
}
