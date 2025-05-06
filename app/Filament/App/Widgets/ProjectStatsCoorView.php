<?php

namespace App\Filament\App\Widgets;

use Dom\Text;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Project;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Forms;
use App\Models\Department;
use App\Models\UserTask;
use App\Services\TrelloTask;
use Illuminate\Support\Facades\Cache;

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
                    ->url(fn ($record): string => \App\Filament\App\Resources\ProjectResource::getUrl('edit', ['record' => $record]))
                    ->searchable(),
                TextColumn::make('end')
                    ->label('Wedding Date')
                    ->date('F j, Y'),
                TextColumn::make('department_progress')
                    ->label('Task per Department Progress')
                    ->getStateUsing(function ($record) {
                        $cacheKey = "project_{$record->id}_department_progress";
                        
                        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($record) {
                            $trelloService = app(TrelloTask::class);
                            $listId = $trelloService->getBoardDepartmentsListId($record->trello_board_id);
                            
                            if (!$listId) {
                                return 'No Trello board found';
                            }
                            
                            $cards = $trelloService->getListCards($listId);
                            $progress = [];
                            
                            // Define the desired order
                            $orderedDepartments = [
                                'Coordination',
                                'Catering',
                                'Hair and Makeup',
                                'Photo and Video',
                                'Designing',
                                'Entertainment'
                            ];
                            
                            // Sort cards according to the desired order
                            usort($cards, function($a, $b) use ($orderedDepartments) {
                                $aIndex = array_search($a['name'], $orderedDepartments);
                                $bIndex = array_search($b['name'], $orderedDepartments);
                                return $aIndex - $bIndex;
                            });
                            
                            // Batch fetch all checklists and items
                            $allChecklists = [];
                            foreach ($cards as $card) {
                                $allChecklists[$card['id']] = $trelloService->getCardChecklists($card['id']);
                            }
                            
                            foreach ($cards as $card) {
                                $totalTasks = 0;
                                $completedTasks = 0;
                                
                                foreach ($allChecklists[$card['id']] as $checklist) {
                                    $items = $trelloService->getChecklistItems($checklist['id']);
                                    $totalTasks += count($items);
                                    $completedTasks += count(array_filter($items, fn($item) => ($item['state'] ?? 'incomplete') === 'complete'));
                                }
                                
                                if ($totalTasks === 0) {
                                    $progress[] = $card['name'] . ': No tasks';
                                    continue;
                                }
                                
                                $percentage = round(($completedTasks / $totalTasks) * 100);
                                
                                // Add color coding based on percentage
                                $color = match (true) {
                                    $percentage >= 80 => 'text-green-600',
                                    $percentage >= 50 => 'text-yellow-600',
                                    default => 'text-red-600'
                                };
                                
                                $progress[] = "<span class='{$color}'>{$card['name']}: {$percentage}%</span>";
                            }
                            
                            return implode("<br>", $progress);
                        });
                    })
                    ->html(),
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
                                fn (Builder $query, $date): Builder => $query->whereDate('end', '>=', $date),
                            )
                            ->when(
                                $data['wedding_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end', '<=', $date),
                            );
                    }),
            ]);
    }
}
