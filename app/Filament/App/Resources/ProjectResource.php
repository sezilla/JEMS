<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ProjectResource\Pages;
use App\Filament\App\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Package;
use App\Models\User;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

$user = Auth::user();

class ProjectResource extends Resource
{
    
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
        ->query(Project::forUser(Auth::user()))
            ->columns([
                ImageColumn::make('thumbnail_path')
                    ->disk('public')
                    ->label('Thumbnail'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('package.name')
                    ->label('Package')
                    ->searchable()
                    ->limit(15),
                TextColumn::make('event_date')
                    ->date()
                    ->sortable(),
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
                TextColumn::make('groom_name')
                    ->searchable()
                    ->limit(15),
                TextColumn::make('bride_name')
                    ->searchable()
                    ->limit(15),
                TextColumn::make('groomCoordinator.name')
                    ->label('Groom Coordinator')
                    ->searchable()
                    ->limit(15),
                TextColumn::make('brideCoordinator.name')
                    ->label('Bride Coordinator')
                    ->searchable()
                    ->limit(15),
                TextColumn::make('headCoordinator.name')
                    ->label('Head Coordinator')
                    ->searchable()
                    ->limit(15),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
    public static function canCreate(): bool
    {
        return false; // Disable create functionality
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            // 'create' => Pages\CreateProject::route('/create'),
            // 'edit' => Pages\EditProject::route('/{record}/edit'),
            'task' => Pages\task::route('/{record}/task'),
        ];
    }
}
