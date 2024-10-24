<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ProjectResource\Pages;
use App\Filament\App\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use App\Models\Package;
use App\Models\User;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                        Forms\Components\Select::make('package_id')
                            ->label('Packages')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->options(Package::all()->pluck('name', 'id')),
                        Forms\Components\MarkdownEditor::make('description')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('event_date')
                            ->required()
                            ->default(now()->toDateString()),
                        Forms\Components\TextInput::make('venue')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make()
                    ->description('Couple Details')
                    ->collapsible()
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('groom_name')
                            ->label('Groom Name')
                            ->required()
                            ->columnSpan(1)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bride_name')
                            ->label('Bride Name')
                            ->columnSpan(1)
                            ->required()
                            ->maxLength(255),
                        ColorPicker::make('theme_color'),
                        Forms\Components\MarkdownEditor::make('special_request')
                            ->label('Special Requests')
                            ->columnSpan(2),
                        FileUpload::make('thumbnail_path')
                            ->disk('public')
                            ->label('Thumbnail')
                            ->directory('thumbnails'),
                    ]),


                Section::make()
                    ->description('Coordinators')
                    ->collapsible()
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('groom_coordinator')
                            ->options(User::all()->pluck('name', 'id'))
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->label('Groom Coordinator')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('bride_coordinator')
                            ->options(User::all()->pluck('name', 'id'))
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->label('Bride Coordinator')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('head_coordinator')
                            ->options(User::all()->pluck('name', 'id'))
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->label('Head Coordinator')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('other_coordinators')
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
                        Forms\Components\Select::make('team1')
                            ->relationship('cateringTeam', 'name') 
                            ->label('Catering')
                                // ->multiple()
                            ->preload()
                            ->searchable(),

                        Forms\Components\Select::make('team2')
                            ->relationship('hairAndMakeupTeam', 'name') 
                            ->label('Hair and Makeup')
                            // ->multiple()
                            ->preload()
                            ->searchable(),

                        Forms\Components\Select::make('team3')
                            ->relationship('photoAndVideoTeam', 'name') 
                            ->label('Photo and Video')
                            // ->multiple()
                            ->preload()
                            ->searchable(),

                        Forms\Components\Select::make('team4')
                            ->relationship('designingTeam', 'name')
                            ->label('Designing')
                            // ->multiple()
                            ->preload()
                            ->searchable(),

                        Forms\Components\Select::make('team5')
                            ->relationship('entertainmentTeam', 'name') 
                            ->label('Entertainment')
                            // ->multiple()
                            ->preload()
                            ->searchable(),

                        Forms\Components\Select::make('team6')
                            ->relationship('driversTeam', 'name') 
                            ->label('Drivers')
                            // ->multiple()
                            ->preload()
                            ->searchable(),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('trello_board_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('package_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('venue')
                    ->searchable(),
                Tables\Columns\TextColumn::make('groom_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bride_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('theme_color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('thumbnail_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('groom_coordinator')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bride_coordinator')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('head_coordinator')
                    ->numeric()
                    ->sortable(),
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
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
