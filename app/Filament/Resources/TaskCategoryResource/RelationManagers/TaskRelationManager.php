<?php

namespace App\Filament\Resources\TaskCategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Department;
use Filament\Tables\Table;
use App\Models\TaskCategory;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Resources\RelationManagers\RelationManager;

class TaskRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Stack::make([
                    Split::make([
                        Tables\Columns\TextColumn::make('name')
                            ->size(TextColumnSize::Large)
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
                                ->size(TextColumnSize::ExtraSmall)
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
                            ->size(TextColumnSize::ExtraSmall)
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
                    Stack::make([
                        Tables\Columns\TextColumn::make('skills.name')
                            ->getStateUsing(function ($record) {
                                return 'Skills Required';
                            })
                            ->size(TextColumnSize::ExtraSmall)
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
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
