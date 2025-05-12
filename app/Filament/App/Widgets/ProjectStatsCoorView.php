<?php

namespace App\Filament\App\Widgets;

use Dom\Text;
use Filament\Tables;
use App\Models\Project;
use App\Models\UserTask;
use App\Models\Department;
use Filament\Tables\Table;
use App\Services\TrelloTask;
use App\Services\DashboardService;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Forms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class ProjectStatsCoorView extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Projects assigned to you';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()->forUser(Auth::user())

            )
            ->columns([
                TextColumn::make('name')
                    ->label('Project Name')
                    ->url(fn($record): string => \App\Filament\App\Resources\ProjectResource::getUrl('task', ['record' => $record]))
                    ->searchable(),
                TextColumn::make('end')
                    ->label('Wedding Date')
                    ->date('F j, Y'),
                TextColumn::make('completed_percentage')
                    ->label('Completed Percentage')
                    ->getStateUsing(function ($record) {
                        $percentages = app(DashboardService::class)->getCardCompletedPercentage($record->id);
                        return $percentages;
                    })
                    ->html()
                    ->alignLeft(),
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
            ]);
    }
}
