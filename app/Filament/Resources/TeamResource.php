<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Section;


class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Select::make('leader_id')
                            ->relationship('leaders', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Team Leader');
                                });
                            })
                            ->label('Team Leader')
                            ->preload()
                            ->searchable(),
                        Forms\Components\MarkdownEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('departments')
                            ->relationship('departments', 'name')
                            ->label('Department')
                            ->preload()
                            ->searchable(),
                        Select::make('members')
                            ->multiple()
                            ->relationship('members', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Member');
                                });
                            })
                            ->label('Members')
                            ->preload()
                            ->searchable(),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('departments.name')
                    ->label('Department')
                    ->searchable()
                    ->limit(15),
                // Display the leader's name
                ImageColumn::make('leaders.avatar_url')
                    ->label('Team Leader')
                    ->searchable()
                    ->circular(),
                ImageColumn::make('members.avatar_url')
                    ->label('Members')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(),


                // ImageColumn::make('member.avatar')
                //     ->circular()
                //     ->stacked(),

                TextColumn::make('description')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
