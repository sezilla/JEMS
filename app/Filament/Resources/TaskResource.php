<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Package;
use App\Models\Department;
use App\Models\TaskCategory;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
    
                Forms\Components\Select::make('package_id')
                    ->label('Package')
                    ->relationship('package', 'name')
                    ->required()
                    ->preload(),
                
                Forms\Components\Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->required()
                    ->preload(),
                
                Forms\Components\Select::make('task_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->required()
                    ->preload(),
                
            ]);
    }
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('package.name')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('department.name')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->limit(25),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->options(function () {
                        return Department::pluck('name', 'id');
                    })
                    ->label('Department')
                    ->relationship('department', 'name'),

                SelectFilter::make('task_category_id')
                    ->options(function () {
                        return TaskCategory::pluck('name', 'id');
                    })
                    ->label('Category')
                    ->relationship('category', 'name'),

                SelectFilter::make('package_id')
                    ->options(function () {
                        return Department::pluck('name', 'id');
                    })
                    ->label('Package')
                    ->relationship('package', 'name'),
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

    public static function getNavigationGroup(): ?string
    {
        return 'Project Management';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
