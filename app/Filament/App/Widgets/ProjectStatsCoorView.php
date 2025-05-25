<?php

namespace App\Filament\App\Widgets;

use Dom\Text;
use Filament\Tables;
use App\Models\Project;
use App\Models\UserTask;
use App\Models\Department;
use Filament\Tables\Table;
use App\Enums\ProjectStatus;
use App\Services\TrelloTask;
use App\Services\DashboardService;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Forms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Filament\Support\Enums\FontWeight;
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
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Infolists\Components\RepeatableEntry;

class ProjectStatsCoorView extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Events assigned to you';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()->forUser(Auth::user())

            )
            ->columns([
                TextColumn::make('name')
                    ->limit(25)
                    ->label('Event Name')
                    ->url(fn($record): string => \App\Filament\App\Resources\ProjectResource::getUrl('task', ['record' => $record]))
                    ->searchable(),
                TextColumn::make('end')
                    ->label('Wedding Date')
                    ->date('F j, Y'),
                TextColumn::make('completed_percentage')
                    ->toggleable()
                    ->label('Completed Percentage')
                    ->getStateUsing(function ($record) {
                        $percentages = app(DashboardService::class)->getCardCompletedPercentage($record->id);
                        return $percentages;
                    })
                    ->html()
                    ->alignLeft(),
                TextColumn::make('overall_progress')
                    ->label('Overall Progress')
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
                TextColumn::make('status')
                    ->badge()
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
            ])
            ->paginated([5])
            ->filters([
                Filter::make('wedding_date_range')
                    ->form([
                        DatePicker::make('wedding_from')
                            ->label('From Date')
                            ->displayFormat('M d, Y')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->firstDayOfWeek(1)
                            ->placeholder('Select start date'),
                        DatePicker::make('wedding_until')
                            ->label('Until Date')
                            ->displayFormat('M d, Y')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->firstDayOfWeek(1)
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['wedding_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('end', '>=', $date),
                            )
                            ->when(
                                $data['wedding_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('end', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ViewAction::make('View Event')
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->color('primary')
                    ->infolist(fn(Project $record) => [
                        Grid::make(2)
                            ->label('View Event Details')
                            ->schema([
                                Section::make('Event Overview')
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
                                                    ->label('Coordinators')
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
                            ])->label('Event Details'),

                    ]),
            ]);
    }
}
