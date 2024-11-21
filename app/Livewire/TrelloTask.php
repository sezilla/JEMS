<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\TrelloService;
use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Contracts\HasTable;


class TrelloTask extends Component implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public $projectId;
    public ?array $trelloCards = null;

    public function mount($projectId)
    {
        $this->projectId = $projectId;
        $this->fetchTrelloCards();
    }

    public function fetchTrelloCards()
    {
        $project = Project::find($this->projectId);
        if ($project && $project->trello_board_id) {
            $trelloService = app(TrelloService::class);
            $this->trelloCards = $trelloService->fetchTrelloCards($project->trello_board_id);
        }
    }

    public function getChecklistItems(): array
    {
        $items = [];
        if (!empty($this->trelloCards)) {
            foreach ($this->trelloCards as $card) {
                if (!empty($card['checklist'])) {
                    foreach ($card['checklist'] as $checklist) {
                        foreach ($checklist['checkItems'] as $checkItem) {
                            $items[] = [
                                'id' => $checkItem['id'],
                                'task' => $checkItem['name'],
                                'state' => $checkItem['state'],
                                'due' => $checkItem['due'] ? date('Y-m-d', strtotime($checkItem['due'])) : 'No deadline',
                                'cardName' => $card['name'], // Card name for context
                            ];
                        }
                    }
                }
            }
        }
        return $items;
    }

    protected function getTableQuery()
    {
        return collect($this->getChecklistItems());
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('task')
                ->label('Task')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('state')
                ->label('State')
                ->sortable(),
            Tables\Columns\TextColumn::make('due')
                ->label('Due Date')
                ->sortable(),
            Tables\Columns\TextColumn::make('cardName')
                ->label('Card Name')
                ->searchable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('markDone')
                ->label('Mark as Done')
                ->action(function ($record) {
                    $this->markAsDone($record['id']);
                })
                ->color('success')
                ->visible(fn ($record) => $record['state'] !== 'complete'),
        ];
    }

    public function markAsDone($checkItemId)
    {
        $trelloService = app(TrelloService::class);
        $trelloService->markChecklistItemAsDone($checkItemId);
        $this->fetchTrelloCards(); // Refresh the cards after marking as done
    }

    public function render()
    {
        return view('livewire.trello-task');
    }
}
