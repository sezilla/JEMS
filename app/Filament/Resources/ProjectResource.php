<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Package;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\ProjectStatus;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Date;

use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables\Actions\ForceDeleteAction;
use App\Filament\Resources\ProjectResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Filament\App\Resources\ProjectResource\Widgets\ProjectDetails;



class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $label = 'Events';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'groom_name',
            'bride_name',
            'package.name',
            'status',
        ];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Name' => $record->name,
            'Groom' => $record->groom_name,
            'Bride' => $record->bride_name,
            'Date' => $record->end?->format('M d, Y'),
            'Package' => $record->package->name,
            'Status' => $record->status?->label(),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make()
                    ->description('Event details')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->visible(fn($livewire) => $livewire instanceof Pages\ViewProject)
                            ->columnSpan(1)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('package_id')
                            ->label('Packages')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->options(Package::all()->pluck('name', 'id'))
                            ->disabled(fn($record) => $record !== null)
                            ->helperText('Select a package'),
                        Forms\Components\MarkdownEditor::make('description')
                            ->columnSpanFull()
                            ->placeholder('Describe the event details, requirements, and any specific notes...'),


                        Forms\Components\DatePicker::make('start')
                            ->columnSpan(1)
                            ->label('Start Date')
                            ->required()
                            ->default(now()->toDateString())
                            ->helperText('Select the start date of the event planning'),
                        Forms\Components\DatePicker::make('end')
                            ->columnSpan(1)
                            ->label('Event Date')
                            ->required()
                            ->default(Carbon::now()->addYear()->toDateString())
                            ->after('start')
                            ->helperText('Select the day of the Wedding')
                            ->rules([
                                function () {
                                    return function ($attribute, $value, $fail) {
                                        $start = request()->input('start');
                                        if ($start) {
                                            $startDate = Carbon::parse($start);
                                            $endDate = Carbon::parse($value);

                                            if ($endDate->lessThan($startDate->addMonths(4))) {
                                                $fail('The event date must be at least  months after the start date.');
                                            }
                                        }
                                    };
                                },
                                function () {
                                    return function ($attribute, $value, $fail) {
                                        $date = Carbon::parse($value);
                                        $projectCount = Project::whereDate('end', $date)->count();

                                        if ($projectCount >= 6) {
                                            $fail('Max number of events (6) has been reached for this date. Please select another date.');
                                        }
                                    };
                                }
                            ]),

                        Forms\Components\TextInput::make('venue')
                            ->label('Location')
                            ->maxLength(255)
                            ->placeholder('Enter the event location')
                    ])
                    ->columns(3),

                Section::make()
                    ->description('Couple Details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('bride_name')
                            ->label('Bride`s Name')
                            ->columnSpan(1)
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter bride\'s name'),
                        Forms\Components\TextInput::make('groom_name')
                            ->label('Groom`s Name')
                            ->required()
                            ->columnSpan(1)
                            ->maxLength(255)
                            ->placeholder('Enter groom\'s name'),
                        Forms\Components\MarkdownEditor::make('special_request')
                            ->label('Special Requests')
                            ->columnSpan('full')
                            ->placeholder('List any special requirements or requests for the event...'),
                        ColorPicker::make('theme_color')
                            ->default('#d095ed')
                            ->label('Legend')
                            ->required()
                            ->helperText('Choose a color to represent this event in the calendar')
                            ->rules([
                                'required',
                                'string',
                                function () {
                                    return function (string $attribute, $value, $fail) {
                                        if (
                                            !preg_match('/^#([0-9a-fA-F]{6})$/', $value) &&
                                            !preg_match('/^[a-zA-Z]+$/', $value)
                                        ) {
                                            $fail('The color must be a valid hex code or color name.');
                                        }
                                    };
                                },
                            ])
                            ->columnSpan(1),

                        FileUpload::make('thumbnail_path')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->columnSpan(1)
                            ->label('Thumbnail')
                            ->directory('thumbnails')
                            ->helperText('Upload a representative image for the event (recommended size: 800x600)')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg'])
                            ->maxSize(5120),
                    ]),

                Section::make()
                    ->description('Coordinators')
                    ->collapsible()
                    ->columns(3)
                    ->schema([
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

                        Forms\Components\Select::make('bride_coordinator')
                            ->options(User::all()->pluck('name', 'id'))
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->label('Bride`s Coordinator')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('groom_coordinator')
                            ->options(User::all()->pluck('name', 'id'))
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->label('Groom`s Coordinator')
                            ->required()
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

                        Forms\Components\Select::make('bride_coor_assistant')
                            ->options(User::all()->pluck('name', 'id'))
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->label('Bride`s Coordinator Assistant')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('groom_coor_assistant')
                            ->options(User::all()->pluck('name', 'id'))
                            ->relationship('coordinators', 'name', function ($query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'Coordinator');
                                });
                            })
                            ->label('Groom`s Coordinator Assistant')
                            ->searchable()
                            ->preload()
                            ->required(),

                    ]),

                Section::make()
                    ->description('Teams')
                    ->visible(fn($livewire) => $livewire instanceof Pages\ViewProject || $livewire instanceof Pages\EditProject)
                    ->collapsible()
                    ->schema([
                        Forms\Components\Repeater::make('teams')
                            ->label('')
                            ->relationship('teams')
                            ->grid(3)
                            ->schema([
                                Forms\Components\Select::make('team_id')
                                    ->label('')
                                    ->options(function () {
                                        return \App\Models\Team::with('departments')
                                            ->get()
                                            ->mapWithKeys(function ($team) {
                                                return [$team->id => $team->name];
                                            });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Add Team')
                            // ->reorderable(true)
                            ->itemLabel(function (array $state): ?string {
                                $teamId = $state['team_id'] ?? null;
                                if (!$teamId) {
                                    return 'Team';
                                }
                                $team = \App\Models\Team::with('departments')->find($teamId);
                                return $team && $team->departments->isNotEmpty()
                                    ? ucfirst($team->departments->first()->name) . ' Team'
                                    : 'Team';
                            })
                            ->saveRelationshipsUsing(function ($record, $state) {
                                $record->teams()->sync(collect($state)->pluck('team_id')->filter());
                            }),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->label('Names')
                        ->searchable()
                        ->limit(34)
                        ->size(TextColumn\TextColumnSize::Large)
                        ->getStateUsing(function ($record) {
                            return $record->groom_name . ' & ' . $record->bride_name;
                        }),
                    Split::make([
                        ImageColumn::make('thumbnail_path')
                            ->disk('public')
                            ->label('Thumbnail')
                            ->width(150)
                            ->height(200)
                            ->extraImgAttributes(['class' => 'rounded-md'])
                            ->defaultImageUrl(url('https://placehold.co/150x200/EEE/gray?text=Event+Image&font=lato')),
                        Stack::make([
                            Stack::make([
                                TextColumn::make('package.name')
                                    ->getStateUsing(function ($record) {
                                        return 'Package';
                                    })
                                    ->size(TextColumnSize::Small)
                                    ->weight(FontWeight::Thin)
                                    ->formatStateUsing(function ($column, $state) {
                                        return '<span style="font-size: 80%; opacity: 0.7;">' . $state . '</span>';
                                    })
                                    ->html(),
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
                                        ->copyMessageDuration(1500),

                                    IconColumn::make('status')
                                        ->label('Status')
                                        ->options([
                                            'heroicon-o-clock' => ProjectStatus::ACTIVE,
                                            'heroicon-o-check-circle' => ProjectStatus::COMPLETED,
                                            'heroicon-o-trash' => ProjectStatus::ARCHIVED,
                                            'heroicon-o-x-circle' => ProjectStatus::CANCELLED,
                                            'heroicon-o-pause-circle' => ProjectStatus::ON_HOLD,
                                        ])
                                        ->colors([
                                            'success' => ProjectStatus::COMPLETED,
                                            'warning' => ProjectStatus::ARCHIVED,
                                            'danger' => ProjectStatus::CANCELLED,
                                            'secondary' => ProjectStatus::ON_HOLD,
                                            'primary' => ProjectStatus::ACTIVE,
                                        ])
                                        ->size('md')
                                        ->tooltip(fn($record) => $record->status?->label()),

                                ])
                                    ->extraAttributes(['class' => 'flex flex-wrap gap-2 items-center']),
                            ]),
                            Stack::make([
                                TextColumn::make('venue')
                                    ->getStateUsing(function ($record) {
                                        return 'Location';
                                    })
                                    ->size(TextColumnSize::Small)
                                    ->weight(FontWeight::Thin)
                                    ->formatStateUsing(function ($column, $state) {
                                        return '<span style="font-size: 80%; opacity: 0.7;">' . $state . '</span>';
                                    })
                                    ->html(),
                                TextColumn::make('venue')
                                    ->limit(15)
                                    ->placeholder('No location'),
                            ]),
                            Stack::make([
                                TextColumn::make('user')
                                    ->getStateUsing(function ($record) {
                                        return 'Event Creator';
                                    })
                                    ->size(TextColumnSize::Small)
                                    ->weight(FontWeight::Thin)
                                    ->formatStateUsing(function ($column, $state) {
                                        return '<span style="font-size: 80%; opacity: 0.7;">' . $state . '</span>';
                                    })
                                    ->html(),
                                Split::make([
                                    ImageColumn::make('user.avatar_url')
                                        ->tooltip('Event Creator')
                                        ->label('Coordinator')
                                        ->circular()
                                        ->width(20)
                                        ->height(20)
                                        ->grow(false),
                                    TextColumn::make('user.name')
                                        ->label('Coordinator')
                                        ->searchable()
                                        ->limit(15),
                                ]),
                            ]),
                            Stack::make([
                                TextColumn::make('end')
                                    ->getStateUsing(function ($record) {
                                        return 'Event Date';
                                    })
                                    ->size(TextColumnSize::Small)
                                    ->weight(FontWeight::Thin)
                                    ->formatStateUsing(function ($column, $state) {
                                        return '<span style="font-size: 80%; opacity: 0.7;">' . $state . '</span>';
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
                                ->size(TextColumnSize::Small)
                                ->weight(FontWeight::Thin)
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 80%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                            TextColumn::make('headCoordinator.name')
                                ->label('Head Coordinator')
                                ->searchable()
                                ->badge()
                                ->limit(8),
                        ]),

                        Stack::make([
                            TextColumn::make('brideCoordinator.name')
                                ->getStateUsing(function ($record) {
                                    return 'Bride`s coor';
                                })
                                ->size(TextColumnSize::Small)
                                ->weight(FontWeight::Thin)
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 80%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                            TextColumn::make('brideCoordinator.name')
                                ->label('Bride Coordinator')
                                ->searchable()
                                ->badge()
                                ->limit(8),
                        ]),

                        Stack::make([
                            TextColumn::make('groomCoordinator.name')
                                ->getStateUsing(function ($record) {
                                    return 'Groom`s coor';
                                })
                                ->size(TextColumnSize::Small)
                                ->weight(FontWeight::Thin)
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 80%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                            TextColumn::make('groomCoordinator.name')
                                ->label('Groom Coordinator')
                                ->searchable()
                                ->badge()
                                ->limit(8),
                        ]),

                    ]),
                    TextColumn::make('teams.name')
                        ->getStateUsing(function ($record) {
                            return 'Teams';
                        })
                        ->size(TextColumn\TextColumnSize::Small)
                        ->weight(FontWeight::Thin)
                        ->formatStateUsing(function ($column, $state) {
                            return '<span style="font-size: 80%; opacity: 0.7;">' . $state . '</span>';
                        })
                        ->html(),
                    ImageColumn::make('teams.image')
                        ->label('Bride Coordinator')
                        ->searchable()
                        ->stacked()
                        ->limit(6)
                        ->circular()
                        ->limitedRemainingText(),

                ])->space(3),
            ])->defaultSort('end', 'asc')
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
                'sm' => 1,
            ])
            ->paginated([12, 24, 48, 96, 'all'])
            ->filters([
                Tables\Filters\Filter::make('completed')
                    ->label('Completed')
                    ->query(fn(Builder $query): Builder => $query->where('status', config('project.project_status.completed'))),
                Tables\Filters\Filter::make('canceled')
                    ->label('Canceled')
                    ->query(fn(Builder $query): Builder => $query->where('status', config('project.project_status.canceled'))),
                Tables\Filters\Filter::make('on_hold')
                    ->label('On Hold')
                    ->query(fn(Builder $query): Builder => $query->where('status', config('project.project_status.on_hold'))),
                Tables\Filters\TrashedFilter::make()
                    ->label('Deleted')
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
