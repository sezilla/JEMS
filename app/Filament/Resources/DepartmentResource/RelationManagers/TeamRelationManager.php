<?php

namespace App\Filament\Resources\DepartmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
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
                        ->relationship('leaders', 'name', function ($query) {
                            $query->whereHas('roles', function ($q) {
                                $q->where('name', 'Team Leader');
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
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
