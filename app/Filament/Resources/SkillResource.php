<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SkillResource\Pages;
use App\Filament\Resources\SkillResource\RelationManagers;
use App\Models\Skill;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Components\Section;


class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Name' => $record->name,
            'Department' => $record->department->name,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make('Skill Information')
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'name')
                            ->preload()
                            ->required()
                            ->multiple(),
                        Forms\Components\MarkdownEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Section::make('Task Information')
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\Select::make('task_id')
                            ->label('Task')
                            ->preload()
                            ->relationship('task', 'name')
                            ->required()
                            ->multiple(),
                    ]),
            ]);
        // ->schema([
        //     Forms\Components\TextInput::make('name')
        //         ->required()
        //         ->maxLength(255),
        //     Forms\Components\Select::make('task_id')
        //         ->label('Task')
        //         ->relationship('task', 'name')
        //         ->required()
        //         ->multiple(),
        //     Forms\Components\Select::make('department_id')
        //         ->label('Department')
        //         ->relationship('department', 'name')
        //         ->required()
        //         ->multiple(),
        //     Forms\Components\MarkdownEditor::make('description')
        //         ->required()
        //         ->columnSpanFull(),
        // ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Split::make([
                        Tables\Columns\TextColumn::make('name')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('task.name')
                            ->searchable()
                            ->limit(40),
                    ]),
                    Split::make([
                        Tables\Columns\TextColumn::make('department.name')
                            ->searchable()
                            ->badge()
                            ->width('10%')
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
                    ]),
                ])
            ])
            // ->columns([


            //     Tables\Columns\TextColumn::make('task.name')
            //         ->searchable()
            //         ->limit(20),

            //     Tables\Columns\TextColumn::make('department.name')
            //         ->searchable()
            //         ->badge()
            //         ->width('10%'),

            //     Tables\Columns\TextColumn::make('description')
            //         ->searchable()
            //         ->limit(15),
            // ])
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
            'index' => Pages\ListSkills::route('/'),
            'create' => Pages\CreateSkill::route('/create'),
            'edit' => Pages\EditSkill::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }
}
