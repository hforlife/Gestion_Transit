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
use Filament\Schemas\Components\Actions\Action;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;

use Filament\Forms\Set;
use Filament\Notifications\Notification;

use Illuminate\Support\HtmlString;

class ColisForm
{
    /* =====================================================
     | SAVE RECORD
     ===================================================== */
    protected static function saveRecord($livewire): void
    {
        $data = $livewire->form->getState();

        if (!$livewire->record) {
            $livewire->record = $livewire->getModel()::create([
                ...$data,
                'etat_colis' => 'BL_ENREGISTRE',
                'status_colis_port' => 'EN_ATTENTE',
                'status_colis_douane' => 'EN_ATTENTE',
                'etat_expertise' => 'EN_ATTENTE',
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
                        ->schema([
                            Section::make('Informations du colis')
                                ->schema([
                                    Grid::make(2)->schema([

                                        TextInput::make('numero_bl')
                                            ->required()
                                            ->unique(ignoreRecord: true),

                                        Select::make('id_type_colis')
                                            ->relationship('typeColis', 'nom')
                                            ->required()
                                            ->preload()
                                            ->searchable(),

                                        Textarea::make('description')
                                            ->columnSpanFull(),

                                        Select::make('client_id')
                                            ->relationship('client', 'nom')
                                            ->required()
                                            ->searchable(),

                                        Select::make('id_port')
                                            ->relationship('port', 'nom')
                                            ->required()
                                            ->searchable(),

                                        Select::make('user_id')
                                            ->relationship('agent', 'name')
                                            ->default(auth()->id())
                                            ->required(),
                                    ]),
                                ]),
                        ])
                        ->afterValidation(fn($livewire) => self::saveRecord($livewire)),

                    /* =====================================================
                     | STEP 2 - PORT
                     ===================================================== */
                    Step::make('Port')
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

                    /* =====================================================
                     | STEP 4 - LIVRAISON
                     ===================================================== */
                    Step::make('Livraison')
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
                ->persistStepInQueryString()
                ->columnSpanFull(),
            ]);
    }
}
