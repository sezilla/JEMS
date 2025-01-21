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
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\FontFamily;

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

                Forms\Components\TextInput::make('duration')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('days'),
                
                Forms\Components\MarkdownEditor::make('description')
                    ->label('Description')
                    ->required(),
    
                Forms\Components\Select::make('package_id')
                    ->label('Package')
                    ->relationship('packages', 'name')
                    ->required()
                    ->preload()
                    ->multiple(),
                
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

                Forms\Components\Select::make('skill_id')
                    ->label('Skills Required')
                    ->relationship('skills', 'name')
                    ->multiple()
                    ->required()
                    ->preload(),
                
            ]);
    }
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    Stack::make([
                        Split::make([
                            Tables\Columns\TextColumn::make('name')
                                ->size(TextColumn\TextColumnSize::Large)
                                // ->weight(FontWeight::SemiBold)
                                ->searchable()
                                ->limit(25)
                                ->grow(false),
                            Stack::make([
                                Tables\Columns\TextColumn::make('department.name')
                                    ->getStateUsing(function ($record) {
                                        return 'Department';
                                    })
                                    ->alignment(Alignment::End)
                                    ->size(TextColumn\TextColumnSize::ExtraSmall)
                                    ->weight(FontWeight::Thin)
                                    ->formatStateUsing(function ($column, $state) {
                                        return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                    })
                                    ->html(),
                                Tables\Columns\TextColumn::make('department.name')
                                    ->searchable()
                                    ->limit(25)
                                    ->badge()
                                    ->alignment(Alignment::End)
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
                            ])
                        ]),
                        Stack::make([
                            Tables\Columns\TextColumn::make('packages.name')
                                ->getStateUsing(function ($record) {
                                    return 'Packages Included';
                                })
                                ->size(TextColumn\TextColumnSize::ExtraSmall)
                                ->weight(FontWeight::Thin)
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                            Tables\Columns\TextColumn::make('packages.name')
                                ->searchable()
                                // ->limit(25)
                                ->badge()
                                ->width('10%')
                                ->color(
                                    fn (string $state): string => match ($state) {
                                        'Ruby' => 'ruby',
                                        'Garnet' => 'garnet',
                                        'Emerald' => 'emerald',
                                        'Infinity' => 'infinity',
                                        'Sapphire' => 'sapphire',
                                        default => 'gray',
                                    }
                                ),
                        ]),
                        Split::make([
                            Stack::make([
                                Tables\Columns\TextColumn::make('duration')
                                    ->getStateUsing(function ($record) {
                                        return 'Prep Duration';
                                    })
                                    ->size(TextColumn\TextColumnSize::ExtraSmall)
                                    ->weight(FontWeight::Thin)
                                    ->formatStateUsing(function ($column, $state) {
                                        return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                    })
                                    ->html(),
                                Tables\Columns\TextColumn::make('duration')
                                    ->searchable()
                                    ->grow(false)
                                    ->suffix(' days'),
                            ])
                            ->grow(false),
                            Stack::make([
                                Tables\Columns\TextColumn::make('category.name')
                                ->getStateUsing(function ($record) {
                                    return 'Prep Timeline';
                                })
                                ->size(TextColumn\TextColumnSize::ExtraSmall)
                                ->weight(FontWeight::Thin)
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                                Tables\Columns\TextColumn::make('category.name')
                                ->searchable()
                                ->limit(20),
                            ])
                        ]), 
                        Stack::make([
                            Tables\Columns\TextColumn::make('skills.name')
                                ->getStateUsing(function ($record) {
                                    return 'Skills Required';
                                })
                                ->size(TextColumn\TextColumnSize::ExtraSmall)
                                ->weight(FontWeight::Thin)
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                            Tables\Columns\TextColumn::make('skills.name')
                                ->searchable()
                                ->badge()
                                ->width('10%'),
                        ]),
                    ])
                    ->space(3),
            ])
            // ->columns([
            //     Tables\Columns\TextColumn::make('name')
            //         ->searchable()
            //         ->limit(20)
            //         ->grow(false),
            //     Tables\Columns\TextColumn::make('duration')
            //         ->searchable()
            //         ->limit(20)
            //         ->grow(false)
            //         ->suffix(' days'),
            //     Tables\Columns\TextColumn::make('description')
            //         ->searchable()
            //         ->limit(15)
            //         ->grow(false)
            //         ->toggleable(isToggledHiddenByDefault: true),
            //     Tables\Columns\TextColumn::make('packages.name')
            //         ->searchable()
            //         ->limit(25)
            //         ->badge()
            //         ->width('10%'),
            //     Tables\Columns\TextColumn::make('department.name')
            //         ->searchable()
            //         ->limit(25)
            //         ->badge()
            //         ->color(
            //             fn (string $state): string => match ($state) {
            //                 'Catering' => 'Catering',
            //                 'Hair and Makeup' => 'Hair',
            //                 'Photo and Video' => 'Photo',
            //                 'Designing' => 'Designing',
            //                 'Entertainment' => 'Entertainment',
            //                 'Coordination' => 'Coordination',
            //             }
            //         ),
            //     Tables\Columns\TextColumn::make('category.name')
            //         ->searchable()
            //         ->limit(15),
            //     Tables\Columns\TextColumn::make('skills.name')
            //         ->searchable()
            //         ->badge()
            //         ->width('10%')
            //         ->toggleable(isToggledHiddenByDefault: true),
            // ])
            ->contentGrid([
                'md' => 3,
                'xl' => 4,
                'sm' => 2,
            ])
            ->paginated([
                20, 40, 80, 'all'
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
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
