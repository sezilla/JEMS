<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Columns\TextColumn;


class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('description')
                ->required()
                ->columnSpanFull(),
            Forms\Components\DatePicker::make('event_date')
                ->required()
                ->default(now()->toDateString()),
            Forms\Components\TextInput::make('venue')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('packages')
                ->relationship('packages', 'name')
                ->label('Packages')
                ->preload()
                ->searchable()
                ->reactive(),
            Forms\Components\Select::make('coordinators')
                ->relationship('coordinators', 'name', function ($query) {
                    $query->whereHas('roles', function ($q) {
                        $q->where('name', 'Coordinator');
                    });
                })
                ->multiple()
                ->label('Coordinators')
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('teams')
                ->relationship('teams', 'name')
                ->multiple()
                ->label('Teams')
                ->preload()
                ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable()
                    ->limit(15)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('event_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('packages.name')
                    ->label('Package')
                    ->searchable()
                    ->limit(15),
                TextColumn::make('user.name')
                    ->label('Creator')
                    ->toggleable(isToggledHiddenByDefault: true),


                    TextColumn::make('coordinators.name')
                    ->label('Coordinators')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if ($record->coordinators) {
                            return implode('<br/>', $record->coordinators->pluck('name')->toArray());
                        }
                        return 'N/A';
                    })
                    ->html()
                    ->verticallyAlignStart(),
                
                TextColumn::make('teams.name')
                    ->label('Teams')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if ($record->teams) {
                            return implode('<br/>', $record->teams->pluck('name')->toArray());
                        }
                        return 'N/A';
                    })
                    ->html()
                    ->verticallyAlignStart(),
                
                
                



                TextColumn::make('venue')
                    ->searchable()
                    ->limit(15),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
