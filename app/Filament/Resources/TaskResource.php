<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Task;
use Filament\Tables;
use App\Models\Package;
use Filament\Forms\Form;
use App\Models\Department;
use Filament\Tables\Table;
use App\Models\TaskCategory;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TaskResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TaskResource\RelationManagers;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Name' => $record->name,
            'Department' => $record->department->name,
            'Package' => $record->packages->pluck('name')->join(', '),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    // ->description('Fill in the details of the task')
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\MarkdownEditor::make('description')
                            ->label('Description')
                            ->required(),
                    ]),
                Section::make()
                    ->description('Fill in the details of the task')
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'name')
                            ->required()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\Select::make('task_category_id')
                            ->label('Duration')
                            ->relationship('category', 'name')
                            ->required()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\Select::make('package_id')
                            ->label('Package')
                            ->relationship('packages', 'name')
                            ->required()
                            ->preload()
                            ->multiple()
                            ->columnSpan(2),

                        Forms\Components\Select::make('skill_id')
                            ->label('Skills Required')
                            ->relationship('skills', 'name')
                            ->multiple()
                            ->required()
                            ->preload()
                            ->columnSpan(2),
                    ])->columns([
                        'sm' => 1,
                        'lg' => 2,
                    ]),

            ])->columns([
                'sm' => 1,
                'lg' => 2,
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
                                // ->searchable()
                                ->limit(25)
                                ->sortable()
                                ->searchable()
                                ->badge()
                                ->alignment(Alignment::End)
                                ->color(
                                    fn(string $state): string => match ($state) {
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
                            // ->searchable()
                            // ->limit(25)
                            ->badge()
                            ->searchable()
                            ->width('10%')
                            ->color(
                                fn(string $state): string => match ($state) {
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
                            Tables\Columns\TextColumn::make('category.name')
                                ->getStateUsing(function ($record) {
                                    return 'Prep Timeline';
                                })
                                ->sortable()
                                ->size(TextColumn\TextColumnSize::ExtraSmall)
                                ->weight(FontWeight::Thin)
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                            Tables\Columns\TextColumn::make('category.name')
                                // ->searchable()
                                ->sortable()
                                ->limit(30),
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
            ->contentGrid([
                'md' => 3,
                'xl' => 4,
                'sm' => 2,
            ])
            ->paginated([
                20,
                40,
                80,
                'all'
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
                    ->label('Duration')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make()
                //     ->tooltip('View task details'),
                // Tables\Actions\EditAction::make()
                //     ->tooltip('Edit task details'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make()
                    //     ->tooltip('Delete selected tasks'),
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
        return 'Event Management';
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
