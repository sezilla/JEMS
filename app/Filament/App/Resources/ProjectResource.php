<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ProjectResource\Pages;
use App\Filament\App\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('trello_board_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('package_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('event_date')
                    ->required(),
                Forms\Components\TextInput::make('venue')
                    ->maxLength(255),
                Forms\Components\TextInput::make('groom_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('bride_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('theme_color')
                    ->maxLength(255),
                Forms\Components\Textarea::make('special_request')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('thumbnail_path')
                    ->maxLength(255),
                Forms\Components\TextInput::make('groom_coordinator')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('bride_coordinator')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('head_coordinator')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('trello_board_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('package_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('venue')
                    ->searchable(),
                Tables\Columns\TextColumn::make('groom_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bride_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('theme_color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('thumbnail_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('groom_coordinator')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bride_coordinator')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('head_coordinator')
                    ->numeric()
                    ->sortable(),
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
                // Tables\Actions\EditAction::make(),
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
    public static function canCreate(): bool
    {
        return false; // Disable create functionality
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            // 'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
