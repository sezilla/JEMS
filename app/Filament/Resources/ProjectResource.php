<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Carbon\Carbon;
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

    protected static ?string $recordTitleAttribute = 'name';

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
                            ->visible(fn($livewire) => $livewire instanceof Pages\ViewProject || $livewire instanceof Pages\EditProject)
                            ->columnSpan(1)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('package_id')
                            ->label('Packages')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->options(Package::all()->pluck('name', 'id'))
                            ->disabled(fn($record) => $record !== null),
                        Forms\Components\MarkdownEditor::make('description')
                            ->columnSpanFull(),


                        Forms\Components\DatePicker::make('start')
                            ->columnSpan(1)
                            ->label('Start Date')
                            ->required()
                            ->default(now()->toDateString()),
                        Forms\Components\DatePicker::make('end')
                            ->columnSpan(1)
                            ->label('Event Date')
                            ->required()
                            ->default(Carbon::now()->addYear()->toDateString()) // Default: 1 year from today
                            ->after('start') // Ensures 'end' is after 'start'
                            ->rules([
                                function () {
                                    return function ($attribute, $value, $fail) {
                                        $start = request()->input('start');
                                        if ($start) {
                                            $startDate = Carbon::parse($start);
                                            $endDate = Carbon::parse($value);

                                            if ($endDate->lessThan($startDate->addMonths(4))) {
                                                $fail('The end date must be at least 4 months after the start date.');
                                            }
                                        }
                                    };
                                }
                            ]),

                        Forms\Components\TextInput::make('venue')
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Section::make()
                    ->description('Couple Details')
                    ->collapsible()
                    ->columns(2)
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
                        Forms\Components\MarkdownEditor::make('special_request')
                            ->label('Special Requests')
                            ->columnSpan('full'),
                        ColorPicker::make('theme_color')
                            ->columnSpan(1),

                        FileUpload::make('thumbnail_path')
                            ->disk('public')
                            ->columnSpan(1)
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
                    // ->visible(fn ($livewire) => $livewire instanceof Pages\ViewProject)
                    ->visible(fn($livewire) => $livewire instanceof Pages\ViewProject || $livewire instanceof Pages\EditProject)
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
            ])->defaultSort('end', 'asc')
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
                'sm' => 1,
            ])
            ->paginated([12, 24, 48, 96, 'all'])
            ->filters([])
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
