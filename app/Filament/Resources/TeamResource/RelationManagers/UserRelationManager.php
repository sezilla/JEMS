<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use Filament\Forms;
use App\Models\Team;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class UserRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('users')
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->grow(false),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('skills.name')
                    ->label('Skills')
                    ->searchable()
                    ->badge()
                    ->width('10%'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make('addMember')
                    ->createAnother(false)
                    ->label('Add Member')
                    ->tooltip('Add new members to this team')
                    ->action(function (array $data) {
                        // Assign the user(s) to the team by updating their team_id
                        User::whereIn('id', (array) $data['user_id'])->update(['team_id' => $this->ownerRecord->id]);

                        // Notify the user
                        Notification::make()
                            ->title('Member added successfully!')
                            ->success()
                            ->send();
                    })
                    ->form([
                        Select::make('user_id')
                            ->label('Select Member')
                            ->options(function () {
                                $team = $this->ownerRecord;
                                $departmentIds = $team->departments->pluck('id');

                                return User::whereHas('departments', function ($query) use ($departmentIds) {
                                    $query->whereIn('departments.id', $departmentIds);
                                })
                                    ->whereNull('team_id')
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->multiple()
                            ->preload()
                            ->searchable(),

                    ])
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->label('Remove')
                    ->tooltip('Remove member from this team')
                    ->action(function (User $record) {
                        // Remove the user from the team by setting team_id to null
                        $record->update(['team_id' => null]);

                        // Optional notification for successful removal
                        Notification::make()
                            ->title('User removed from team successfully!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Remove Member')
                    ->modalSubheading(fn(User $record): string => 'Are you sure you want to remove ' . $record->name . ' from ' . $this->ownerRecord->name . '?')
                    ->modalButton('Yes, remove')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip('Remove selected members from this team'),
                ]),
            ]);
    }
}
