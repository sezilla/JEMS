<?php

namespace App\Filament\Resources\DepartmentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Department;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TeamResource\Pages\ViewTeam;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\TeamResource\Pages\CreateTeam;

class TeamRelationManager extends RelationManager
{
    protected static string $relationship = 'teams';

    public function form(Form $form): Form
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
                        Select::make('leader_id')
                            ->label('Team Leader')
                            ->options(function () {
                                $department = $this->ownerRecord;

                                if (!$department) {
                                    return [];
                                }

                                return \App\Models\User::whereHas('roles', function ($q) {
                                    $q->where('name', 'Team Leader');
                                })
                                    ->whereHas('departments', function ($q) use ($department) {
                                        $q->where('departments.id', $department->id);
                                    })
                                    ->whereDoesntHave('teams')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

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
                            ->visible(fn($livewire) => $livewire instanceof CreateTeam),
                    ])

            ])
            ->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('image')
                    ->label('Team Photo')
                    ->width(50)
                    ->height(50)
                    ->rounded('lg'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('leaders.name')
                    ->label('Team Leader'),
                ImageColumn::make('members.avatar_url')
                    ->circular()
                    ->label('Members')
                    ->stacked(),
            ])
            ->recordUrl(fn($record) => route(ViewTeam::getRouteName(), [
                'record' => $record,
            ]))
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->createAnother(false)
                    ->tooltip('Create a new team in this department'),
            ])
            ->actions([
                // Tables\Actions\EditAction::make()
                //     ->tooltip('Edit team details'),
                // Tables\Actions\DeleteAction::make()
                //     ->tooltip('Delete this team'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make()
                    //     ->tooltip('Delete selected teams'),
                ]),
            ]);
    }
}
