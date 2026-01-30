<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Size;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('telephone')
                    ->searchable(),

                TextColumn::make('roles')
                    ->label('Rôle')
                    ->getStateUsing(fn($record) => $record->roles->pluck('name')->join(', '))
                    ->searchable(),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Actif'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->recordActions([
                ActionGroup::make([
                    // Voir
                    ViewAction::make()
                        ->label('Voir')
                        ->icon('heroicon-o-eye')
                        ->color('success'),

                    // Modifier
                    EditAction::make()
                        ->label('Modifier')
                        ->icon('heroicon-o-pencil')
                        ->color('primary'),

                    // Changer Mot de passe
                    Action::make('changePassword')
                        ->label('Changer le mot de passe')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->modalHeading('Changer le mot de passe')
                        ->modalSubmitActionLabel('Mettre à jour')
                        ->modalWidth('md')
                        ->schema([
                            TextInput::make('current_password')
                                ->label('Ancien mot de passe')
                                ->password()
                                ->required(),

                            TextInput::make('password')
                                ->label('Nouveau mot de passe')
                                ->password()
                                ->required()
                                ->minLength(8),

                            TextInput::make('password_confirmation')
                                ->label('Confirmation du mot de passe')
                                ->password()
                                ->required()
                                ->same('password'),
                        ])
                        ->action(function ($record, array $data) {
                            // Vérification ancien mot de passe
                            if (!Hash::check($data['current_password'], $record->password)) {
                                throw ValidationException::withMessages([
                                    'current_password' => 'L’ancien mot de passe est incorrect.',
                                ]);
                            }

                            // Mise à jour sécurisée
                            $record->update([
                                'password' => Hash::make($data['password']),
                            ]);
                        })
                        ->visible(fn($record) => $record->is_active),

                    // Supprimer
                    DeleteAction::make()
                        ->label('Supprimer')
                        ->icon('heroicon-o-trash')
                        ->color('danger'),

                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(Size::Small)
                    ->color('primary')
                    ->button(),
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
