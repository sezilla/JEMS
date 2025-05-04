<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Project;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\App\Resources\ProjectResource;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Http\Controllers\ProjectReportController;


class OverallReports extends BaseWidget
{
    protected static ?int $sort = 5;

    protected static ?string $model = Project::class;
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Overall Reports of Projects';

    public function table(Table $table): Table
    {
        return $table
        ->headerActions([
            Action::make('report')
            ->label('Export Report')
            ->icon('heroicon-o-document-text')
            ->requiresConfirmation()
            ->action(function (OverallReports $livewire) {
                // 1) Grab the full filters state:
                $filters = $livewire->getTableFiltersForm()->getState();
        
                // 2) Unpack the wedding_date_range array:
                $range  = $filters['wedding_date_range'] ?? [];
        
                $from   = $range['wedding_date_from']  ?? null;
                $until  = $range['wedding_date_until'] ?? null;
                $status = $range['status']             ?? null;
        
                // 3) Build query params:
                $queryParams = [];
        
                if ($from) {
                    $queryParams['start'] = Carbon::parse($from)->format('Y-m-d');
                }
        
                if ($until) {
                    $queryParams['end']   = Carbon::parse($until)->format('Y-m-d');
                }
        
                if ($status) {
                    $queryParams['status'] = $status;
                }
        
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
                    ->label('Project Name')
                    ->url(fn (Project $record): string => ProjectResource::getUrl('edit', ['record' => $record]))
                    ->searchable()
                    ->limit(30),
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
                    ->searchable(),
                TextColumn::make('end')
                    ->label('Wedding Date')
                    ->description(fn (Project $record): string => 
                        $record->start ? 'started at ' . Carbon::parse($record->start)->format('F j, Y') : 'N/A'
                    )
                    ->date($format = 'F j, Y'),
                TextColumn::make('headCoordinator.name')
                    ->label('Head Coordinator')
                    ->description(fn (Project $record): string => 
                        ($record->groomCoordinator->name ?? 'N/A') . ' & ' . ($record->brideCoordinator->name ?? 'N/A')
                    ),
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
                    ->html()
            ])
            ->filters([
                Filter::make('wedding_date_range')
                    ->label('Wedding Date Range')
                    ->form([
                        DatePicker::make('wedding_date_from')
                            ->label('From (Day–Month–Year)')
                            ->native(false)
                            ->displayFormat('F j, Y'),
            
                        DatePicker::make('wedding_date_until')
                            ->label('Until (Day–Month–Year)')
                            ->native(false)
                            ->displayFormat('F j, Y'),
            
                        Select::make('status')
                            ->label('Status')
                            ->options(
                                collect(config('project.project_status'))
                                    ->mapWithKeys(fn ($value, $key) => [$key => ucfirst(str_replace('_', ' ', $key))])
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
                            $query->where('status', $data['status']);
                        }
            
                        return $query;
                    }),
                ]);
            
            
    }
}