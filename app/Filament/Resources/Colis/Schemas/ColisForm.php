<?php

namespace App\Filament\Resources\Colis\Schemas;

use App\Models\DossierTransit;
use App\Models\TypeColis;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;

class ColisForm
{
    /* =====================================================
     | Update Dossier Reference
     ===================================================== */
    protected static function updateDossierReference($record): void
    {
        if (!$record?->id_dossier_transit) {
            return;
        }

        $dossier = DossierTransit::find($record->id_dossier_transit);

        if (!$dossier) {
            return;
        }

        // Récupérer les types d'unités du BL
        $typesUnites = $record->unites()
            ->pluck('type')
            ->unique()
            ->toArray();

        if (empty($typesUnites)) {
            return;
        }

        /**
         * Déterminer le type principal
         * Priorité métier :
         * CHASSIS_MACHINE
         * CHASSIS_VOITURE
         * CHASSIS
         * CONTENEUR
         */
        $typePrincipal = collect([
            'CHASSIS_MACHINE',
            'CHASSIS_VOITURE',
            'CHASSIS',
            'CONTENEUR',
        ])->first(fn($type) => in_array($type, $typesUnites));

        // Préfixe selon le type
        $prefix = match ($typePrincipal) {
            'CHASSIS_MACHINE' => 'CM',
            'CHASSIS_VOITURE' => 'CV',
            'CHASSIS' => 'CH',
            'CONTENEUR' => 'C',
            default => 'C',
        };

        $year = Carbon::now()->year;

        $count = DossierTransit::where('reference', 'like', "{$prefix}{$year}-%")
            ->where('id', '!=', $dossier->id)
            ->count();

        $nextNumber = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        $reference = "{$prefix}{$year}-{$nextNumber}";

        $dossier->update([
            'reference' => $reference,
        ]);
    }

    /* =====================================================
     | SAVE RECORD
     ===================================================== */
    protected static function saveRecord($livewire): void
    {
        $data = $livewire->form->getState();

        // 🔥 Suppression des champs qui ne sont plus dans la table colis
        // (ils ont été déplacés vers colis_unites)
        unset($data['num_t1']);
        unset($data['etat_t1']);
        unset($data['declaration_reference']);
        unset($data['date_entree_douane']);
        unset($data['date_sortie_douane']);
        unset($data['status_douane']);
        unset($data['num_pvc']);
        unset($data['num_ae']);
        unset($data['num_cmc']);
        unset($data['etat_expertise']);
        unset($data['etat_pvc']);
        unset($data['etat_ae']);
        unset($data['etat_cmc']);
        unset($data['date_livraison']); // sera géré par unité
        unset($data['status_colis_livraison']); // sera géré par unité

        // 🔥 Ajouter l'utilisateur connecté
        $data['user_id'] = Auth::id();

        if (!$livewire->record) {
            $livewire->record = $livewire->getModel()::create([
                ...$data,
                'etat_colis' => 'BL_ENREGISTRE',
                'status_colis_port' => 'EN_ATTENTE',
                'status' => 'EN_COURS',
            ]);

            // 🔥 Mise à jour automatique de la référence du dossier
            self::updateDossierReference($livewire->record);
        } else {
            $livewire->record->update($data);

            // 🔥 Si on change le type ou le dossier
            self::updateDossierReference($livewire->record);
        }
    }

    /* =====================================================
     | COMPLETE STEP - Simplifié car les étapes sont gérées par unité
     ===================================================== */
    protected static function completeStep(string $step, $record): void
    {
        if (!$record) {
            return;
        }

        // Le BL n'a plus d'étapes détaillées, seulement un statut global
        // qui peut être calculé à partir des unités
        $record->touch(); // Met à jour updated_at
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
                            return $user->hasAnyRole(['super_admin', 'agent-saisie']);
                        })
                        ->schema([
                            Section::make('Informations générales du BL')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextInput::make('numero_bl')
                                            ->label('Numéro BL')
                                            ->required()
                                            ->rule(function ($livewire) {
                                                return Rule::unique('colis', 'numero_bl')
                                                    ->ignore($livewire->record?->id);
                                            }),

                                        Select::make('id_type_colis')
                                            ->label('Type de colis principal')
                                            ->relationship('typeColis', 'nom')
                                            ->required()
                                            ->preload()
                                            ->searchable()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                // Déterminer si c'est un type à unités multiples
                                                $type = TypeColis::find($state);
                                                $typesMultiUnites = ['CHASSIS', 'CHASSIS_VOITURE', 'CHASSIS_MACHINE', 'Véhicules'];
                                                $typeUpper = strtoupper($type?->nom ?? '');
                                                $isMultiUnites = in_array($typeUpper, $typesMultiUnites) 
                                                    || str_contains(strtolower($type?->nom ?? ''), 'véhicule')
                                                    || str_contains(strtolower($type?->nom ?? ''), 'chassis');
                                                $set('is_multi_unites', $isMultiUnites);
                                            }),

                                        \Filament\Forms\Components\Hidden::make('is_multi_unites')->default(false),

                                        Textarea::make('description')
                                            ->label('Description du BL')
                                            ->columnSpanFull(),

                                        Select::make('id_dossier_transit')
                                            ->label('Dossier de transit')
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
                                            ->label('Port d\'entrée')
                                            ->relationship('port', 'nom')
                                            ->required()
                                            ->preload()
                                            ->searchable(),

                                        Select::make('user_id')
                                            ->label('Agent(s) responsable(s)')
                                            ->relationship('agent', 'name')
                                            ->default(auth()->id())
                                            ->preload()
                                            ->required(),
                                    ]),
                                ]),

                            /* =====================================================
                             | REPEATER POUR LES UNITÉS (VÉHICULES/CHÂSSIS)
                             ===================================================== */
                            Section::make('Unités du BL')
                                ->description(
                                    fn($get) =>
                                    $get('is_multi_unites')
                                    ? 'Ce BL peut contenir plusieurs unités'
                                    : 'Ce BL contient une unité'
                                )
                                ->schema([
                                    Repeater::make('unites')
                                        ->relationship('unites')
                                        ->label('')
                                        ->schema([
                                            Select::make('type')
                                                ->label('Type d\'unité')
                                                ->options([
                                                    'CONTENEUR' => 'Conteneur',
                                                    'CHASSIS' => 'Châssis',
                                                    'CHASSIS_VOITURE' => 'Châssis Voiture',
                                                    'CHASSIS_MACHINE' => 'Châssis Machine',
                                                ])
                                                ->required()
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                    // Auto-détection
                                                    if (in_array($state, ['CHASSIS_VOITURE', 'CHASSIS_MACHINE'])) {
                                                        $set('etat', 'AU_PORT');
                                                    }
                                                }),

                                            // Conteneur
                                            TextInput::make('numero_conteneur')
                                                ->label('Numéro Conteneur')
                                                ->visible(fn($get) => $get('type') === 'CONTENEUR')
                                                ->required(fn($get) => $get('type') === 'CONTENEUR'),

                                            // Châssis (tous types)
                                            TextInput::make('numero_chassis')
                                                ->label('Numéro Châssis')
                                                ->visible(
                                                    fn($get) =>
                                                    in_array($get('type'), [
                                                        'CHASSIS',
                                                        'CHASSIS_VOITURE',
                                                        'CHASSIS_MACHINE'
                                                    ])
                                                )
                                                ->required(
                                                    fn($get) =>
                                                    in_array($get('type'), [
                                                        'CHASSIS',
                                                        'CHASSIS_VOITURE',
                                                        'CHASSIS_MACHINE'
                                                    ])
                                                ),

                                            // VIN (uniquement pour voitures et machines)
                                            TextInput::make('vin')
                                                ->label('Numéro VIN')
                                                ->visible(
                                                    fn($get) =>
                                                    in_array($get('type'), [
                                                        'CHASSIS_VOITURE',
                                                        'CHASSIS_MACHINE'
                                                    ])
                                                )
                                                ->required(
                                                    fn($get) =>
                                                    in_array($get('type'), [
                                                        'CHASSIS_VOITURE',
                                                        'CHASSIS_MACHINE'
                                                    ])
                                                )
                                                ->maxLength(17)
                                                ->helperText('17 caractères alphanumériques'),

                                            // État actuel simplifié pour la création
                                            Select::make('etat')
                                                ->label('État initial')
                                                ->options([
                                                    'AU_PORT' => 'Au Port',
                                                ])
                                                ->default('AU_PORT')
                                                ->disabled()
                                                ->helperText('L\'état sera mis à jour dans la gestion détaillée'),
                                        ])
                                        ->columns(2)
                                        ->createItemButtonLabel('Ajouter une unité')
                                        ->minItems(1)
                                        ->collapsible()
                                        ->cloneable()
                                        ->itemLabel(
                                            fn(array $state): ?string =>
                                            $state['numero_chassis'] ?? $state['numero_conteneur'] ?? 'Nouvelle unité'
                                        )
                                ])
                                ->visible(fn($get) => $get('id_type_colis')),

                            // ✅ Note explicative sur la gestion des unités
                            Html::make('info_gestion')
                                ->content(new HtmlString(
                                    '<div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-sm text-blue-800 font-medium">Gestion des unités</span>
                                        </div>
                                        <p class="text-sm text-blue-700 mt-2">
                                            Les informations douanières (T1, déclaration) et d\'expertise (PVC, AE, CMC) 
                                            seront saisies individuellement pour chaque unité dans la vue détaillée.
                                        </p>
                                    </div>'
                                ))
                                ->columnSpanFull(),
                        ])
                        ->afterValidation(fn($livewire) => self::saveRecord($livewire)),

                    /* =====================================================
                     | STEP 2 - APERÇU (remplace les anciennes étapes)
                     ===================================================== */
                    Step::make('Aperçu')
                        ->visible(function ($get, $livewire) {
                            $user = auth()->user();
                            if (!$user) return false;
                            return $livewire->record !== null;
                        })
                        ->schema([
                            Section::make('Récapitulatif du BL')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('numero_bl')
                                            ->label('Numéro BL')
                                            ->default(fn($livewire) => $livewire->record?->numero_bl),

                                        TextEntry::make('typeColis.nom')
                                            ->label('Type principal')
                                            ->default(fn($livewire) => $livewire->record?->typeColis?->nom),

                                        TextEntry::make('unites_count')
                                            ->label('Nombre d\'unités')
                                            ->default(fn($livewire) => $livewire->record?->unites->count())
                                            ->badge()
                                            ->color('primary'),
                                    ]),

                                    // Résumé des unités
                                    \Filament\Schemas\Components\View::make('filament.components.unites-resume')
                                        ->viewData([
                                            'unites' => fn($livewire) => $livewire->record?->unites ?? collect(),
                                        ])
                                        ->visible(fn($livewire) => $livewire->record?->unites()->exists()),

                                    Html::make('info_suivi')
                                        ->content(new HtmlString(
                                            '<div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                                                <p class="text-sm text-green-700">
                                                    ✅ Le BL a été créé avec succès. Pour suivre l\'évolution de chaque unité 
                                                    (douane, expertise, livraison), rendez-vous dans la vue détaillée du BL.
                                                </p>
                                            </div>'
                                        ))
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->afterValidation(fn($livewire) => self::saveRecord($livewire)),
                ])
                ->persistStepInQueryString('step')
                ->columnSpanFull(),
            ]);
    }
}