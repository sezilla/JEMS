<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use App\Models\User;
use App\Models\Team;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class UserRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Member')
                    ->options(User::whereDoesntHave('teams', function ($query) {
                        $query->where('teams.id', $this->ownerRecord->id);
                    })->pluck('name', 'id'))
                    ->required()
                    ->label('Select Member')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('users')
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('skills.name')
                    ->label('Skills')
                    ->searchable()
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('addMember')
                    ->label('Add Member')
                    ->action(function (array $data) {
                        // Attach the user as a member of the team
                        $this->ownerRecord->members()->attach($data['user_id']);
                        
                        // Notify the user
                        Notification::make()
                            ->title('Member added successfully!')
                            ->success()
                            ->send();
                    })
                    ->form([
                        Select::make('user_id')
                            ->label('Member')
                            ->options(User::whereDoesntHave('teams', function ($query) {
                                $query->where('teams.id', $this->ownerRecord->id);
                            })->pluck('name', 'id'))
                            ->required()
                            ->label('Select Member')
                            ->searchable(),
                    ])
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->label('Remove')
                    ->action(function (User $record) {
                        // Detach the user from the team instead of deleting the user
                        $this->ownerRecord->members()->detach($record->id);

                        // Optional notification for successful removal
                        Notification::make()
                            ->title('User removed from team successfully!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Remove Member')
                    ->modalSubheading(fn (User $record): string => 'Are you sure you want to remove ' . $record->name . ' from ' . $this->ownerRecord->name . '?')
                    ->modalButton('Yes, remove')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
