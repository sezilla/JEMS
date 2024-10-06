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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;


class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make()
                    ->description('Project details')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                        Forms\Components\Select::make('packages')
                            ->relationship('packages', 'name')
                            ->label('Packages')
                            ->preload()
                            ->searchable()
                            ->reactive(),
                        Forms\Components\MarkdownEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('event_date')
                            ->required()
                            ->default(now()->toDateString()),
                        Forms\Components\TextInput::make('venue')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make()
                    ->description('Couple Details')
                    ->collapsible()
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('Groom_name')
                            ->label('Groom Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('Bride_name')
                            ->label('Bride Name')
                            ->required()
                            ->maxLength(255),
                        ColorPicker::make('theme_color')
                            ->required(),
                        Forms\Components\MarkdownEditor::make('requests')
                            ->label('Special Requests')
                            ->required()
                            ->columnSpan(2),
                        FileUpload::make('thumbnail')
                            ->disk('public')
                            ->directory('thumbnails'),
                    ]),


                Section::make()
                    ->description('Coordinators')
                    ->collapsible()
                    ->columns(4)
                    ->schema([
                        Forms\Components\Select::make('coordinators')
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->label('Groom Coordinator')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('coordinators')
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->label('Bride Coordinator')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('coordinators')
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->label('Head Coordinator')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('coordinators')
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->multiple()
                            ->label('Other Coordinators')
                            ->searchable()
                            ->preload(),
                    ]),
                Section::make()
                    ->columns(3)
                    ->description('Teams')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('teams')
                            ->relationship('teams', 'name')
                            ->label('Catering')
                            ->preload()
                            ->searchable(),
                        Forms\Components\Select::make('teams')
                            ->relationship('teams', 'name')
                            ->label('Hair and Makeup')
                            ->preload()
                            ->searchable(),
                        Forms\Components\Select::make('teams')
                            ->relationship('teams', 'name')
                            ->label('Photo and Video')
                            ->preload()
                            ->searchable(),
                        Forms\Components\Select::make('teams')
                            ->relationship('teams', 'name')
                            ->label('Designing')
                            ->preload()
                            ->searchable(),
                        Forms\Components\Select::make('teams')
                            ->relationship('teams', 'name')
                            ->label('Entertainment')
                            ->preload()
                            ->searchable(),
                        Forms\Components\Select::make('teams')
                            ->relationship('teams', 'name')
                            ->label('Drivers')
                            ->preload()
                            ->searchable(),
                    ]),
                
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
                TextColumn::make('packages.name')
                    ->label('Package')
                    ->searchable()
                    ->limit(15),
                TextColumn::make('description')
                    ->searchable()
                    ->limit(15)
                    ->toggleable(isToggledHiddenByDefault: true),
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
