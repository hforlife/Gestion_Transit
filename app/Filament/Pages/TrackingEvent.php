<?php

namespace App\Filament\Pages;

use App\Models\Colis;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\Colis\Pages\ViewColisTracking;

class TrackingEvent extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::MapPin;
    protected static ?string $navigationLabel = 'Suivi des colis';
    protected static ?string $title = 'Suivi des colis';
    protected static string | UnitEnum | null $navigationGroup = 'Colis';
    protected static ?int $navigationSort = 3;
    
    protected string $view = 'filament.pages.tracking-event';
    
    public ?array $data = [];
    public Colis $record;

    public function mount(Colis $record): void
    {
        $this->record = $record;
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('etat_colis')
                    ->label('Statut')
                    ->options([
                        'BL_ENREGISTRE' => 'BL enregistré',
                        'AU_PORT' => 'Arrivé au port',
                        'A_LA_DOUANE' => 'À la douane',
                        'EN_ROUTE' => 'En route',
                        'LIVRE' => 'Livré',
                    ])
                    ->required(),
                    
                Textarea::make('commentaire')
                    ->label('Commentaire')
                    ->rows(3),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateStatus')
                ->label('Mettre à jour le statut')
                ->action('updateStatus')
                ->color('primary'),
                
            Action::make('back')
                ->label('Retour au colis')
                // ->url(fn() => ViewColisTracking::getUrl(['record' => $this->record->id]))
                ->color('gray'),
        ];
    }

    public function updateStatus(): void
    {
        $this->validate([
            'data.etat_colis' => 'required',
        ]);

        $this->record->etat_colis = $this->data['etat_colis'];
        $this->record->save();

        // Ajouter un événement de tracking manuel
        $this->record->trackingEvents()->create([
            'step' => $this->data['etat_colis'],
            'label' => match ($this->data['etat_colis']) {
                'BL_ENREGISTRE' => 'BL enregistré',
                'AU_PORT' => 'Arrivé au port',
                'A_LA_DOUANE' => 'À la douane',
                'EN_ROUTE' => 'En route',
                'LIVRE' => 'Livré',
                default => 'Statut mis à jour',
            },
            'commentaire' => $this->data['commentaire'] ?? null,
            'user_id' => auth()->id(),
        ]);

        Notification::make()
            ->title('Statut mis à jour avec succès')
            ->success()
            ->send();

        $this->form->fill(['commentaire' => '']);
    }
}