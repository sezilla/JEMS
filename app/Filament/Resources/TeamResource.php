<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Department;
use Filament\Tables\Columns\Layout\Split;






class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->columnSpan('full')
                            ->required()
                            ->maxLength(255),
                            Select::make('departments')
                            ->relationship('departments', 'name')
                            ->label('Department')
                            ->preload()
                            ->searchable()
                            ->reactive() // Make the field reactive
                            ->afterStateUpdated(fn (callable $set) => $set('leader_id', null)), // Reset leader_id when department changes
                        
                        Select::make('leader_id')
                            ->relationship('leaders', 'name', function ($query, $get) {
                                $departmentId = $get('departments'); // Get the selected department ID
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Team Leader');
                                })->whereHas('departments', function ($q) use ($departmentId) {
                                    $q->where('departments.id', $departmentId); // Filter by selected department
                                });
                            })
                            ->label('Team Leader')
                            ->preload()
                            ->searchable(),
                        
                        Forms\Components\MarkdownEditor::make('description')
                            ->required()
                            ->columnSpan('full'),
                        ]),
                Section::make()
                    ->columnSpan(1)
                    ->columns(1)
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->columnSpan('1')
                            ->directory('teams')
                            ->label('Team Photo'),
                        Select::make('members')
                            ->multiple()
                            ->relationship('members', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Member');
                                });
                            })
                            ->label('Members')
                            ->preload()
                            ->searchable()
                            ->visible(fn ($livewire) => $livewire instanceof Pages\CreateTeam),
                    ])

            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Split::make([
                        ImageColumn::make('image')
                            ->width(150)
                            ->height(150)
                            ->rounded('lg')
                            ->alignment(Alignment::Center),
                        Stack::make([
                            TextColumn::make('name')
                                ->searchable()
                                ->weight(FontWeight::Bold),
                            TextColumn::make('departments.name')
                                ->limit(15),
                            ImageColumn::make('leaders.avatar_url')
                                ->circular(),
                            TextColumn::make('leaders.name')
                                ->label('Team Leaders'),
                        ]),
                        
                    ]),
                    ImageColumn::make('users.avatar_url')
                        ->circular()
                        ->stacked()
                        ->limit(7)
                        ->limitedRemainingText(),
                ])->space(3),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->paginated([12, 24, 48, 96, 'all'])
            ->filters([
                SelectFilter::make('department')
                    ->options(function () {
                        return Department::pluck('name', 'id');
                    })
                    ->label('Department')
                    ->relationship('departments', 'name')
            ])
            
            
            
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UserRelationManager::class,
            RelationManagers\ProjectRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
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
