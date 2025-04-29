<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Project;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use App\Filament\App\Resources\ProjectResource;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;



class OverallReports extends BaseWidget
{
    protected static ?int $sort = 5;

    protected static ?string $model = Project::class;
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Overall Reports of Projects';
    public function table(Table $table): Table
    {
        return $table
            ->query(Project::query())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Project Name')
                    ->url(fn (Project $record): string => ProjectResource::getUrl('edit', ['record' => $record]))
                    ->searchable(),

                TextColumn::make('package.name')
                    ->label('Package Name')
                    ->searchable(),  
                TextColumn::make('package_pricing')
                    ->label('Package Pricing')
                    ->getStateUsing(function ($record): string {
                        $prices = [
                            'Ruby'     => '130,000 Php',
                            'Garnet'   => '165,000 Php',
                            'Emerald'  => '190,000 Php',
                            'Infinity' => '250,000 Php',
                            'Sapphire' => '295,000 Php',
                        ];
                        return $prices[$record->package->name] ?? 'N/A';
                    }),
                TextColumn::make('venue')
                    ->label('Venue')
                    ->searchable(),
                TextColumn::make('start')
                    ->label('Start Date')
                    ->date($format = 'F j, Y'),
                TextColumn::make('end')
                    ->label('Wedding Date')
                    ->date($format = 'F j, Y'),
                TextColumn::make('headCoordinator.name')
                    ->label('Head Coordinator'),
                TextColumn::make('groomCoordinator.name')
                    ->label('Groom Coordinator'),
                TextColumn::make('brideCoordinator.name')
                    ->label('Bride Coordinator'),
                TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record): string {
                        $statuses = [
                            10  => 'Active',
                            200 => 'Completed',
                            100 => 'Archived',
                            0   => 'Canceled',
                            50  => 'On Hold',
                        ];
                        return $statuses[$record->status] ?? 'Unknown';
                    })
                    ->colors([
                        'primary'   => 'Active',
                        'success'   => 'Completed',
                        'secondary' => 'Archived',
                        'danger'    => 'Canceled',
                        'warning'   => 'On Hold',
                    ]),
                

                    
            ])
            ->filters([
                Filter::make('wedding_date_range')
                    ->label('Wedding Date Range')
                    ->form([
                        DatePicker::make('wedding_date_from')
                            ->label('From (Month–Year)')
                            ->native()
                            ->extraInputAttributes(['type' => 'month']),
                        DatePicker::make('wedding_date_until')
                            ->label('Until (Month–Year)')
                            ->native()
                            ->extraInputAttributes(['type' => 'month']),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['wedding_date_from'])) {
                            $from = Carbon::createFromFormat('Y-m', $data['wedding_date_from'])
                                          ->startOfMonth();
                            $query->whereDate('end', '>=', $from);
                        }
                        if (! empty($data['wedding_date_until'])) {
                            $until = Carbon::createFromFormat('Y-m', $data['wedding_date_until'])
                                            ->endOfMonth();
                            $query->whereDate('end', '<=', $until);
                        }
                        return $query;
                    }),
            ]);
            
    }
}
