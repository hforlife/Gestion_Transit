<?php

namespace App\Filament\Widgets;

use App\Models\TrackingEvent;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class LastTrackingEvents extends BaseWidget
{
    protected static ?string $heading = 'Derniers événements de suivi';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TrackingEvent::query()
                    ->with(['trackable', 'user'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('trackable.numero_bl')
                    ->label('Colis')
                    ->searchable()
                    ->badge(),

                TextColumn::make('label')
                    ->label('Étape')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'BL enregistré' => 'gray',
                        'Arrivé au port' => 'warning',
                        'À la douane' => 'danger',
                        'En route' => 'info',
                        'Livré' => 'success',
                        default => 'secondary',
                    }),

                TextColumn::make('user.name')
                    ->label('Agent')
                    ->placeholder('—'),

                TextColumn::make('commentaire')
                    ->label('Commentaire')
                    ->limit(40)
                    ->wrap(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10]);
    }
}
