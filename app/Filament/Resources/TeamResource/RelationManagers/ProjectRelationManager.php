<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\FontFamily;
use Illuminate\Support\Facades\Date;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables\Columns\ColorColumn;

use App\Models\Project;

class ProjectRelationManager extends RelationManager
{
    protected static string $relationship = 'Projects';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->recordTitleAttribute('name')
            ->columns([
                Stack::make([
                    Split::make([
                        ImageColumn::make('thumbnail_path')
                            ->disk('public')
                            ->label('Thumbnail')
                            ->width(150)
                            ->height(200)
                            ->extraImgAttributes(['class' => 'rounded-md']),
                        Stack::make([
                            TextColumn::make('groom_name')
                                ->label('Names')
                                ->searchable()
                                ->size(TextColumn\TextColumnSize::Large)
                                ->getStateUsing(function ($record) {
                                    return $record->groom_name . ' & ' . $record->bride_name;
                                }),
                            TextColumn::make('description')
                                ->limit(40)
                                ->searchable(),
                            Split::make([
                                TextColumn::make('package.name')
                                    ->label('Package')
                                    ->searchable()
                                    ->limit(15)
                                    ->badge()
                                    ->color(
                                        fn(string $state): string => match ($state) {
                                            'Ruby' => 'ruby',
                                            'Garnet' => 'garnet',
                                            'Emerald' => 'emerald',
                                            'Infinity' => 'infinity',
                                            'Sapphire' => 'sapphire',
                                            default => 'gray',
                                        }
                                    ),
                                ColorColumn::make('theme_color')
                                    ->label('Theme Color')
                                    ->copyable()
                                    ->copyMessage('Color code copied')
                                    ->copyMessageDuration(1500)
                            ]),

                            TextColumn::make('venue'),
                            Stack::make([
                                TextColumn::make('start')
                                    ->date()
                                    // ->sortable()
                                    ->formatStateUsing(function ($column, $state) {
                                        return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                    })
                                    ->html(),
                                TextColumn::make('end')
                                    ->label('Event Date')
                                    ->date()
                                    ->fontFamily(FontFamily::Mono)
                                    ->size(TextColumn\TextColumnSize::Large)
                                    ->alignment(Alignment::Left),
                            ]),

                        ])->space(3),
                    ]),
                    Split::make([
                        Stack::make([
                            TextColumn::make('headCoordinator.name')
                                ->getStateUsing(function ($record) {
                                    return 'Head coor';
                                })
                                ->size(TextColumn\TextColumnSize::ExtraSmall)
                                ->weight(FontWeight::Thin)
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                            TextColumn::make('headCoordinator.name')
                                ->label('Head Coordinator')
                                ->searchable()
                                ->badge()
                                ->limit(8),
                        ]),
                        Stack::make([
                            TextColumn::make('groomCoordinator.name')
                                ->getStateUsing(function ($record) {
                                    return 'Groom coor';
                                })
                                ->size(TextColumn\TextColumnSize::ExtraSmall)
                                ->weight(FontWeight::Thin)
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                            TextColumn::make('groomCoordinator.name')
                                ->label('Groom Coordinator')
                                ->searchable()
                                ->badge()
                                ->limit(8),
                        ]),
                        Stack::make([
                            TextColumn::make('brideCoordinator.name')
                                ->getStateUsing(function ($record) {
                                    return 'Bride coor';
                                })
                                ->size(TextColumn\TextColumnSize::ExtraSmall)
                                ->weight(FontWeight::Thin)
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                            TextColumn::make('brideCoordinator.name')
                                ->label('Bride Coordinator')
                                ->searchable()
                                ->badge()
                                ->limit(8),
                        ]),

                    ]),
                    TextColumn::make('teams.name')
                        ->getStateUsing(function ($record) {
                            return 'Teams';
                        })
                        ->size(TextColumn\TextColumnSize::ExtraSmall)
                        ->weight(FontWeight::Thin)
                        ->formatStateUsing(function ($column, $state) {
                            return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                        })
                        ->html(),
                    ImageColumn::make('teams.image')
                        ->label('Bride Coordinator')
                        ->searchable()
                        ->stacked()
                        ->limit(6)
                        ->circular()
                        ->limitedRemainingText(),
                    // TextColumn::make('start')
                    //     ->label('Date Added')
                    //     ->date()
                    //     ->sortable(),


                ])->space(3),
            ])
            ->filters([
                //
            ])
            // ->headerActions([
            //     Tables\Actions\CreateAction::make(),
            // ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
