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
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Department;
use App\Models\TaskCategory;
use Filament\Forms\Get;
use App\Models\PackageTask;




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
                ->disabled(fn (Get $get) => $get('id') !== null)
                ->afterStateUpdated(function (callable $set) {
                    $set('name', null); // Reset name field when department changes
                }),

            Forms\Components\Select::make('task_category_id')
                ->label('Category')
                ->relationship('category', 'name') 
                ->disabled(fn (Get $get) => $get('id') !== null)
                ->required()
                ->preload(),
            
            Forms\Components\TextInput::make('name')
                ->label('Task')
                ->required(), 
            
            Forms\Components\Select::make('skill_id')
                ->label('Skills Required')
                ->relationship('skills', 'name')
                ->multiple()
                ->required()
                ->preload(),
                
            Forms\Components\MarkdownEditor::make('description')
                ->label('Description')
                ->required()
                ->columnSpanFull(),
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
                            default => 'gray',
                        }
                    ),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('name'),
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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Task'),
            
                    Tables\Actions\Action::make('addTask')
                    ->label('Add Task')
                    ->action(function (array $data) {
                        $taskId = $data['task_id']; // Fetch task ID
                        $packageId = $this->ownerRecord->id; // Get current package ID
                
                        // Check if task already exists in the package
                        $exists = PackageTask::where('package_id', $packageId)
                            ->where('task_id', $taskId)
                            ->exists();
                
                        if ($exists) {
                            Notification::make()
                                ->title('Task already exists in this package!')
                                ->danger()
                                ->send();
                            return;
                        }
                
                        // If it doesn't exist, add it
                        PackageTask::create([
                            'package_id' => $packageId,
                            'task_id' => $taskId,
                        ]);
                
                        Notification::make()
                            ->title('Task added successfully!')
                            ->success()
                            ->send();
                    })
                    ->form([
                        Forms\Components\Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'name') 
                            ->required()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set) {
                                $set('task_id', null); // Reset task field when department changes
                            }),
                
                        Forms\Components\Select::make('task_id')
                            ->label('Task')
                            ->options(function ($get) {
                                $departmentId = $get('department_id');
                                return $departmentId 
                                    ? Task::where('department_id', $departmentId)->pluck('name', 'id') 
                                    : [];
                            })
                            ->required()
                            ->preload(),
                        ]),                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('removeTask')
                    ->label('Remove')
                    ->color('danger')
                    ->icon('heroicon-s-trash')
                    ->action(function (Task $record) {
                        $packageId = $this->ownerRecord->id; // Get the current package ID
                        
                        // Detach the task from the package in the task_package table
                        // $this->ownerRecord->tasks()->detach($record->id);

                        PackageTask::where('package_id', $packageId)
                            ->where('task_id', $record->id)
                            ->delete();

                        Notification::make()
                            ->title('Task removed from package successfully!')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
