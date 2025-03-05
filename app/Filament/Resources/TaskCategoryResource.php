<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskCategoryResource\Pages;
use App\Filament\Resources\TaskCategoryResource\RelationManagers;
use App\Models\TaskCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;

class TaskCategoryResource extends Resource
{
    protected static ?string $model = TaskCategory::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                // TextInput::make('start_percentage')
                //     ->required()
                //     ->numeric()
                //     ->suffix('%')
                //     ->default(0)
                //     ->afterStateHydrated(function (TextInput $component, $state) {
                //         $component->state($state * 100);
                //     })
                //     ->dehydrateStateUsing(function ($state) {
                //         return $state / 100;
                //     })
                //     ->minValue(0)
                //     ->maxValue(100),
                // TextInput::make('max_percentage')
                //     ->required()
                //     ->numeric()
                //     ->suffix('%')
                //     ->default(0)
                //     ->afterStateHydrated(function (TextInput $component, $state) {
                //         // Convert the stored decimal value to a percentage for display
                //         $component->state($state * 100);
                //     })
                //     ->dehydrateStateUsing(function ($state) {
                //         // Convert the percentage back to a decimal for storage
                //         return $state / 100;
                //     })
                //     ->minValue(0)
                //     ->maxValue(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('start_percentage')
                //     ->label('progress range')
                //     ->formatStateUsing(fn ($record) => 
                //         ($record->start_percentage * 100) . '% - ' . ($record->max_percentage * 100) . '%'
                //     )
                //     ->searchable(),
            ])
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

    public static function getNavigationGroup(): ?string
    {
        return 'Project Management';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaskCategories::route('/'),
            'create' => Pages\CreateTaskCategory::route('/create'),
            'edit' => Pages\EditTaskCategory::route('/{record}/edit'),
        ];
    }
}
