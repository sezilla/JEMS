<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Project;
use Filament\Tables\Table;
use App\Enums\ProjectStatus;
use App\Services\TrelloTask;
use Illuminate\Support\Carbon;
use App\Services\DashboardService;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Cache;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\Split;
use Filament\Support\Enums\IconPosition;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\App\Resources\ProjectResource;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Http\Controllers\ProjectReportController;
use Filament\Infolists\Components\RepeatableEntry;


class OverallReports extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $model = Project::class;
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Event Reports';

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('report')
                    ->label('Export Report')
                    ->icon('heroicon-o-document-text')
                    ->requiresConfirmation()
                    ->action(function (OverallReports $livewire) {

                        $filters = $livewire->getTableFiltersForm()->getState();


                        $range  = $filters['wedding_date_range'] ?? [];

                        $from   = $range['wedding_date_from']  ?? null;
                        $until  = $range['wedding_date_until'] ?? null;
                        $status = $range['status']             ?? null;


                        $queryParams = [];

                        if ($from) {
                            $queryParams['start'] = Carbon::parse($from)->format('Y-m-d');
                        }

                        if ($until) {
                            $queryParams['end']   = Carbon::parse($until)->format('Y-m-d');
                        }

                        if ($status) {
                            $queryParams['status'] = $status;
                            
                            $queryParams['title'] = match($status) {
                                'completed' => 'Completed Events Reports',
                                'active' => 'Current Active Events',
                                'archived' => 'All Archived Events',
                                'cancelled' => 'All Cancelled Events',
                                'on_hold' => 'On Hold Events',
                                default => 'Overall Reports of Events'
                            };
                        } else {
                            $queryParams['title'] = 'Overall Reports of Events';
                        }

                        // Show notification before redirect
                        \Filament\Notifications\Notification::make()
                            ->title('Exporting Report')
                            ->body('Your PDF report is being generated and will download shortly.')
                            ->success()
                            ->send();

                        // 4) Redirect to your PDF download route with the correct keys:
                        return redirect()->route('projects.report.download', $queryParams);
                    })
                    ->modalHeading('Export Project Report')
                    ->modalDescription('Generate a PDF report of exactly what you see.')
                    ->modalSubmitActionLabel('Export PDF'),
            ])

            ->query(Project::query())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Event Name')
                    ->url(fn(Project $record): string => ProjectResource::getUrl('edit', ['record' => $record]))
                    ->searchable()
                    ->limit(20),
                TextColumn::make('package.name')
                    ->label('Package Name')
                    ->searchable()
                    ->description(function (Project $record): string {
                        $prices = [
                            'Ruby'     => '130,000 Php',
                            'Garnet'   => '165,000 Php',
                            'Emerald'  => '190,000 Php',
                            'Infinity' => '250,000 Php',
                            'Sapphire' => '295,000 Php',
                        ];

                        return $prices[$record->package->name] ?? 'Price not available';
                    }),
                TextColumn::make('venue')
                    ->label('Location')
                    ->placeholder('No location')
                    ->toggleable()
                    ->limit(20)
                    ->searchable(),
                TextColumn::make('end')
                    ->label('Wedding Date')
                    ->description(
                        fn(Project $record): string =>
                        $record->start ? 'started at ' . Carbon::parse($record->start)->format('F j, Y') : 'N/A'
                    )
                    ->date($format = 'F j, Y'),
                TextColumn::make('headCoordinator.name')
                    ->label('Head Coordinator')
                    ->description(
                        fn(Project $record): string => (substr($record->groomCoordinator->name ?? 'N/A', 0, 8)) . ' & ' . (substr($record->brideCoordinator->name ?? 'N/A', 0, 8))
                    ),
                TextColumn::make('status')
                    ->label('Status')
                    ->toggleable()
                    ->getStateUsing(function ($record): string {
                        return $record->status instanceof ProjectStatus
                            ? $record->status->label()
                            : ProjectStatus::tryFrom((int) $record->status)?->label() ?? 'Unknown';
                    })
                    ->colors([
                        'info'      => ProjectStatus::ACTIVE->label(),
                        'primary'   => ProjectStatus::COMPLETED->label(),
                        'warning'   => ProjectStatus::ARCHIVED->label(),
                        'danger'    => ProjectStatus::CANCELLED->label(),
                        'secondary' => ProjectStatus::ON_HOLD->label(),
                    ]),
                TextColumn::make('teams.name')
                    ->label('Teams Assigned to Event')
                    ->getStateUsing(function ($record) {
                        $teams = $record->teams->pluck('name')->toArray();
                        if ($record->package->name === 'Ruby') {
                            $teams = array_filter($teams, function ($team) {
                                return $team !== 'Photo&Video';
                            });
                        }
                        return implode("<br>", $teams);
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->html(),
                TextColumn::make('department_progress')
                    ->label('Task Per Department Progress')
                    ->getStateUsing(function ($record) {
                        $percentages = app(DashboardService::class)->getCardCompletedPercentage($record->id);
                        return $percentages;
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->html(),
                TextColumn::make('overall_progress')
                    ->label('Progress')
                    ->toggleable()
                    ->getStateUsing(function ($record) {
                        $percentages = app(\App\Services\ProjectService::class)->getProjectProgress($record);

                        // Calculate average percentage
                        if (empty($percentages)) {
                            return 'No data available';
                        }

                        $total = array_sum($percentages);
                        $count = count($percentages);
                        $average = $count > 0 ? round($total / $count) : 0;

                        // Determine color based on percentage
                        $color = '#10b981'; // Default green
                        if ($average < 30) {
                            $color = '#ef4444'; // red
                        } elseif ($average < 70) {
                            $color = '#f59e0b'; // amber
                        }

                        return "<div class='text-center'><span style='color: {$color}; font-weight: bold;'>{$average}%</span></div>";
                    })
                    ->html(),
            ])
            ->filters([
                Filter::make('wedding_date_range')
                    ->label('Wedding Date Range')
                    ->form([
                        DatePicker::make('wedding_date_from')
                            ->label('From Date')
                            ->displayFormat('M d, Y')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->firstDayOfWeek(1)
                            ->placeholder('Select start date'),

                        DatePicker::make('wedding_date_until')
                            ->label('Until Date')
                            ->displayFormat('M d, Y')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->firstDayOfWeek(1)
                            ->placeholder('Select end date'),

                        Select::make('status')
                            ->label('Status')
                            ->options(
                                collect(config('project.project_status'))
                                    ->mapWithKeys(fn($value, $key) => [$key => ucfirst(str_replace('_', ' ', $key))])
                                    ->toArray()
                            ),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['wedding_date_from'])) {
                            $query->whereDate('end', '>=', Carbon::parse($data['wedding_date_from']));
                        }

                        if (!empty($data['wedding_date_until'])) {
                            $query->whereDate('end', '<=', Carbon::parse($data['wedding_date_until']));
                        }

                        if (!empty($data['status'])) {
                            $statusInt = config('project.project_status')[$data['status']] ?? null;

                            if ($statusInt !== null) {
                                $query->where('status', $statusInt);
                            }
                        }


                        return $query;
                    }),
            ])
            ->actions([
                ViewAction::make('View Event')
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->tooltip('View the event details')
                    ->color('primary')
                    ->infolist(fn(Project $record) => [
                        Grid::make(2)
                            ->label('View Event Details')
                            ->schema([
                                Section::make('Event Overview')
                                    ->label('Event Overview')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                ImageEntry::make('thumbnail_path')
                                                    ->label('Event Photo')
                                                    ->grow(false)
                                                    ->width(150)
                                                    ->height(200)
                                                    ->extraImgAttributes(['class' => 'rounded-md'])
                                                    ->columnSpan(1)
                                                    ->defaultImageUrl(url('https://placehold.co/150x200/EEE/gray?text=Event+Image&font=lato')),

                                                Grid::make(1)
                                                    ->schema([
                                                        TextEntry::make('name')
                                                            ->label('Event Name')
                                                            ->weight(FontWeight::Bold)
                                                            ->size(TextEntry\TextEntrySize::Large),

                                                        TextEntry::make('description')
                                                            ->placeholder('No description')
                                                            ->markdown()
                                                            ->columnSpanFull(),

                                                        Grid::make(2)
                                                            ->schema([
                                                                TextEntry::make('package.name')
                                                                    ->label('Package')
                                                                    ->icon('heroicon-m-gift')
                                                                    ->iconPosition(IconPosition::Before),

                                                                TextEntry::make('venue')
                                                                    ->label('Venue')
                                                                    ->placeholder('No location yet')
                                                                    ->icon('heroicon-m-map-pin')
                                                                    ->iconPosition(IconPosition::Before),
                                                            ]),
                                                    ])
                                                    ->columnSpan(1),
                                            ]),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        Section::make('Event Details')
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        TextEntry::make('groom_name')
                                                            ->label('Groom')
                                                            ->icon('heroicon-m-user')
                                                            ->iconPosition(IconPosition::Before),

                                                        TextEntry::make('bride_name')
                                                            ->label('Bride')
                                                            ->icon('heroicon-m-user')
                                                            ->iconPosition(IconPosition::Before),
                                                    ]),

                                                Split::make([
                                                    ColorEntry::make('theme_color')
                                                        ->grow(false)
                                                        ->label('Legend'),

                                                    TextEntry::make('theme_color')
                                                        ->label('color')
                                                        ->badge()
                                                        ->color(fn(string $state): string => $state),
                                                ]),

                                                Split::make([
                                                    TextEntry::make('start')
                                                        ->label('Date Started')
                                                        ->date('F j, Y')
                                                        ->icon('heroicon-m-calendar')
                                                        ->iconPosition(IconPosition::Before),

                                                    TextEntry::make('end')
                                                        ->label('Event Date')
                                                        ->date('F j, Y')
                                                        ->icon('heroicon-m-calendar')
                                                        ->iconPosition(IconPosition::Before),
                                                ]),

                                                TextEntry::make('special_request')
                                                    ->label('Special Requests')
                                                    ->markdown()
                                                    ->columnSpanFull(),
                                            ]),

                                        Section::make('Status & Management')
                                            ->schema([
                                                Split::make([
                                                    TextEntry::make('status')
                                                        ->getStateUsing(function ($record): string {
                                                            $status = $record->status instanceof ProjectStatus
                                                                ? $record->status
                                                                : ProjectStatus::tryFrom((int) $record->status);

                                                            return $status?->label() ?? 'Unknown';
                                                        })
                                                        ->badge()
                                                        ->color(function ($record): string {
                                                            $status = $record->status instanceof ProjectStatus
                                                                ? $record->status
                                                                : ProjectStatus::tryFrom((int) $record->status);

                                                            return $status?->color() ?? 'gray';
                                                        }),

                                                    TextEntry::make('user.name')
                                                        ->label('Created By')
                                                        ->icon('heroicon-m-user-circle')
                                                        ->iconPosition(IconPosition::Before),

                                                    IconEntry::make('trello_board_id')
                                                        ->label('Trello Board')
                                                        ->boolean()
                                                        ->trueIcon('heroicon-m-check-circle')
                                                        ->falseIcon('heroicon-m-x-circle')
                                                        ->trueColor('success')
                                                        ->falseColor('danger'),
                                                ]),

                                            ]),
                                    ]),

                                Section::make('Coordinators')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('headCoordinator.name')
                                                    ->label('Head Coordinator')
                                                    ->icon('heroicon-m-user-circle')
                                                    ->iconPosition(IconPosition::Before),

                                                TextEntry::make('brideCoordinator.name')
                                                    ->label('Bride Coordinator')
                                                    ->icon('heroicon-m-user-circle')
                                                    ->iconPosition(IconPosition::Before),

                                                TextEntry::make('groomCoordinator.name')
                                                    ->label('Groom Coordinator')
                                                    ->icon('heroicon-m-user-circle')
                                                    ->iconPosition(IconPosition::Before),

                                                TextEntry::make('headAssistant.name')
                                                    ->label('Head Coordinator Assistant')
                                                    ->icon('heroicon-m-user')
                                                    ->iconPosition(IconPosition::Before)
                                                    ->visible(fn($record) => !empty($record->headAssistant?->name)),

                                                TextEntry::make('brideAssistant.name')
                                                    ->label('Bride Coordinator Assistant')
                                                    ->icon('heroicon-m-user')
                                                    ->iconPosition(IconPosition::Before)
                                                    ->visible(fn($record) => !empty($record->brideAssistant?->name)),

                                                TextEntry::make('groomAssistant.name')
                                                    ->label('Groom Coordinator Assistant')
                                                    ->icon('heroicon-m-user')
                                                    ->iconPosition(IconPosition::Before)
                                                    ->visible(fn($record) => !empty($record->groomAssistant?->name)),
                                            ]),
                                    ]),

                                Section::make('Teams')
                                    ->schema([
                                        RepeatableEntry::make('teams')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('name')
                                                    ->label('')
                                                    ->icon('heroicon-m-users')
                                                    ->iconPosition(IconPosition::Before),
                                            ])
                                            ->grid(3)->columnSpan('full'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
