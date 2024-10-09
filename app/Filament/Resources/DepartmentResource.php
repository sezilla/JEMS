<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Section;


class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\MarkdownEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('teams')
                            ->multiple()
                            ->relationship('teams', 'name')
                            // ->relationship('teams', 'name'
                            // , function ($query) {
                            //     $query->whereDoesntHave('departments'); //ano to para di lumabas mga team na may department na
                            // }
                            // )
                            ->label('Teams')
                            ->preload()
                            ->searchable(),
                    ])
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->limit(35),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(30),
                // Tables\Columns\TextColumn::make('teams.name')
                //     ->label('Teams')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('teams.name')
                    ->label('Teams')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        // Assuming $record->teams is a collection of teams related to the department
                        return implode('<br/>', $record->teams->pluck('name')->toArray());
                    })
                    ->html()  // Enable HTML rendering for line breaks
                    ->verticallyAlignStart(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
