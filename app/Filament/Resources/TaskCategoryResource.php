<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\TaskCategory;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TaskCategoryResource\Pages;
use App\Filament\Resources\TaskCategoryResource\RelationManagers;

class TaskCategoryResource extends Resource
{
    protected static ?string $model = TaskCategory::class;
    protected static ?string $navigationLabel = 'Task Duration';
    protected static ?string $navigationGroup = 'Event Management';
    protected static ?string $slug = 'task-duration';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Task Duration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->columnSpan(1),
                        MarkdownEditor::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn($record) => Str::limit($record->description, 25))
                    ->searchable()
                    ->limit(25)
                    ->width('40%'),

                TextColumn::make('tasks')
                    ->label('Tasks')
                    ->getStateUsing(function ($record) {
                        return $record->tasks
                            ->pluck('name')
                            ->map(fn($name) => e($name))
                            ->implode('<br>');
                    })
                    ->html()
                    ->wrap()
                    ->limit(100),

            ])
            ->filters([
                //
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
            RelationManagers\TaskRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaskCategories::route('/'),
            'create' => Pages\CreateTaskCategory::route('/create'),
            'view' => Pages\ViewTaskCategories::route('/{record}'),
            'edit' => Pages\EditTaskCategory::route('/{record}/edit'),
        ];
    }
}
