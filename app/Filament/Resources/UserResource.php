<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Select;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->default('password')
                    ->required()
                    ->maxLength(255)
                    ->visible(fn ($livewire) => $livewire instanceof Pages\CreateUser),
                Forms\Components\Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->label('Role')
                    ->preload()
                    ->searchable(),
                // Select::make('departments')
                //     ->relationship('departments', 'name') 
                //     ->label('Department')
                //     ->preload()
                //     ->searchable(),
                // Select::make('team')
                //     ->relationship('team', 'name')
                //     ->label('Team')
                //     ->preload()
                //     ->searchable(),
                // to be fix
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles')
                    ->label('Role')
                    ->verticallyAlignStart()
                    ->getStateUsing(function ($record) {
                        if ($record->roles) {
                            return implode('<br/>', $record->roles->pluck('name')->toArray());
                        }
                        return 'No Role';
                    })
                    ->html(),
                // Panel::make([
                //         Stack::make([
                //                 //DEPARTMENT
                //             TextColumn::make('departments.name')
                //                 ->label('Department')
                //                 ->searchable()
                //                 ->limit(20),
    
                //                 //TEAM
                //             TextColumn::make('team.name')
                //                 ->label('Team')
                //                 ->searchable()
                //                 ->verticallyAlignStart()
                //                 ->getStateUsing(function ($record) {
                //                     if ($record->team) {
                //                         return implode('<br/>', $record->team->pluck('name')->toArray());
                //                     }
                //                     return 'No Team';
                //                 })
                                
                //                 ->html(),
                //         ]),
                //     ])->collapsible(),
                // ikaw jo bahala dto d ko to alam
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }
}
