<?php

namespace App\Filament\Resources\DepartmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Task;

class TaskRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('package_id')
                    ->label('Package')
                    ->relationship('packages', 'name')
                    ->required()
                    ->preload()
                    ->multiple(),
                Forms\Components\Select::make('task_category_id')
                    ->label('Duration')
                    ->relationship('category', 'name')
                    ->required()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('packages.name')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Duration')
                    ->searchable()
                    ->limit(15)
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->tooltip('Add a new task to this department'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->tooltip('Edit task details'),
                Tables\Actions\DeleteAction::make()
                    ->tooltip('Delete this task'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip('Delete selected tasks'),
                ]),
            ]);
    }
}
