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

use App\Models\Package;
use App\Models\User;
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
                            ->columnSpan(2)
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
                        Forms\Components\DatePicker::make('start')
                            ->columnSpan(1)
                            ->label('Start Date')
                            ->required()
                            ->default(now()->toDateString()), 
                        Forms\Components\DatePicker::make('end')
                            ->columnSpan(1)
                            ->label('End Date')
                            ->required()
                            ->default(now()->toDateString()),
                        Forms\Components\TextInput::make('venue')
                            ->maxLength(255),
                    ])
                    ->columns(3),

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
                        Forms\Components\Select::make('groom_coor_assistant')
                            ->options(User::all()->pluck('name', 'id'))
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->label('Groom Coordinator Assistant')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('bride_coor_assistant')
                            ->options(User::all()->pluck('name', 'id'))
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->label('Bride Coordinator Assistant')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('head_coor_assistant')
                            ->options(User::all()->pluck('name', 'id'))
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->label('Head Coordinator Assistant')
                            ->searchable()
                            ->preload(),
                        // Forms\Components\Select::make('other_coordinators')
                        //     ->relationship('coordinators', 'name', function ($query) {
                        //         $query->whereHas('roles', function ($q) {
                        //             $q->where('name', 'Coordinator');
                        //         });
                        //     })
                        //     ->multiple()
                        //     ->label('Other Coordinators')
                        //     ->searchable()
                        //     ->preload(),
                        
                    ]),

                Section::make()
                    ->columns(3)
                    ->description('Teams')
                    ->visible(fn ($livewire) => $livewire instanceof Pages\ViewProject)
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
                            ->relationship('coordinationTeam', 'name') 
                            ->label('Other Coordination')
                            // ->multiple()
                            ->preload()
                            ->searchable(),

                    ]),
            ]);
    }
    

    public static function table(Table $table): Table
    {
        return $table
            // ->columns([
            //     ImageColumn::make('thumbnail_path')
            //         ->disk('public')
            //         ->label('Thumbnail'),
            //     TextColumn::make('name')
            //         ->searchable(),
            //     TextColumn::make('package.name')
            //         ->label('Package')
            //         ->searchable()
            //         ->limit(15),
            //     TextColumn::make('start')
            //         ->date()
            //         ->sortable(),
            //     TextColumn::make('end')
            //         ->date()
            //         ->sortable(),
            //     TextColumn::make('user.name')
            //         ->label('Creator')
            //         ->toggleable(isToggledHiddenByDefault: true),
            //         // ->visible(fn (ViewAction $livewire) => $livewire instanceof ViewAction),
            //     TextColumn::make('coordinators.name')
            //         ->label('Coordinators')
            //         ->searchable()
            //         ->getStateUsing(function ($record) {
            //             if ($record->coordinators) {
            //                 return implode('<br/>', $record->coordinators->pluck('name')->toArray());
            //             }
            //             return 'N/A';
            //         })
            //         ->html()
            //         ->verticallyAlignStart(),
                
            //     TextColumn::make('teams.name')
            //         ->label('Teams')
            //         ->searchable()
            //         ->getStateUsing(function ($record) {
            //             if ($record->teams) {
            //                 return implode('<br/>', $record->teams->pluck('name')->toArray());
            //             }
            //             return 'N/A';
            //         })
            //         ->html()
            //         ->verticallyAlignStart(),
            //     TextColumn::make('venue')
            //         ->searchable()
            //         ->limit(15),
            //     TextColumn::make('groom_name')
            //         ->searchable()
            //         ->limit(15),
            //     TextColumn::make('bride_name')
            //         ->searchable()
            //         ->limit(15),
                    
            //     TextColumn::make('groomCoordinator.name')
            //         ->label('Groom Coordinator') 
            //         ->searchable()
            //         ->limit(15),
                
            //     TextColumn::make('brideCoordinator.name') 
            //         ->label('Bride Coordinator') 
            //         ->searchable()
            //         ->limit(15),
                
            //     TextColumn::make('headCoordinator.name') 
            //         ->label('Head Coordinator') 
            //         ->searchable()
            //         ->limit(15),
                




            //     // TextColumn::make('created_at')
            //     //     ->dateTime()
            //     //     ->sortable()
            //     //     ->toggleable(isToggledHiddenByDefault: true)
            //     //     ->visible(fn (ViewAction $livewire) => $livewire instanceof ViewAction),
                
            //     // TextColumn::make('updated_at')
            //     //     ->dateTime()
            //     //     ->sortable()
            //     //     ->toggleable(isToggledHiddenByDefault: true)
            //     //     ->visible(fn (ViewAction $livewire) => $livewire instanceof ViewAction),
            // ])




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
                            TextColumn::make('name')
                                ->searchable(),
                            Split::make([
                                TextColumn::make('package.name')
                                    ->label('Package')
                                    ->searchable()
                                    ->limit(15)
                                    ->badge()
                                    ->color(
                                        fn (string $state): string => match ($state) {
                                            'Ruby' => 'ruby',
                                            'Garnet' => 'garnet',
                                            'Emerald' => 'emerald',
                                            'Infinity' => 'infinity',
                                            'sapphire' => 'sapphire',
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
                                    ->sortable()
                                    ->formatStateUsing(function ($column, $state) {
                                        return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                    })
                                    ->html(),
                                TextColumn::make('end')
                                    ->label('Event Date')
                                    ->date()
                                    ->sortable()
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
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'view' => Pages\ViewProject::route('/{record}/view'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}