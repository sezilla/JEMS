<?php

namespace App\Filament\Resources\PackageResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Task;

class TaskRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('department_id')
                ->label('Department')
                ->relationship('department', 'name') 
                ->required()
                ->preload()
                ->reactive() // Reacts to changes
                ->afterStateUpdated(function (callable $set) {
                    $set('name', null); // Reset name field when department changes
                }),
            
            Forms\Components\Select::make('name')
                ->label('Task')
                ->options(function ($get) {
                    // Fetch only tasks related to the selected department
                    $departmentId = $get('department_id');
                    return $departmentId 
                        ? Task::where('department_id', $departmentId)->pluck('name', 'id')
                        : [];
                })
                ->required()
                ->preload(),
            
            Forms\Components\Select::make('task_category_id')
                ->label('Category')
                ->relationship('category', 'name') 
                ->required()
                ->preload(),
            
            ]);
    }
    

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Name')
            ->columns([
                Tables\Columns\TextColumn::make('department.name')
                    ->badge()
                    ->color(
                        fn (string $state): string => match ($state) {
                            'Catering' => 'Catering',
                            'Hair and Makeup' => 'Hair',
                            'Photo and Video' => 'Photo',
                            'Designing' => 'Designing',
                            'Entertainment' => 'Entertainment',
                            'Coordination' => 'Coordination',
                        }
                    ),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
