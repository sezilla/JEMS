<?php

namespace App\Filament\App\Resources\ProjectResource\Pages;

use App\Filament\App\Resources\ProjectResource;
use Filament\Resources\Pages\Page;
use App\Services\TrelloService;
use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class Task extends Page
{
    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.app.resources.project-resource.pages.task';

    public ?array $trelloCards = null;

    public function mount($record)
    {
        $project = Project::find($record);
        $trelloBoardId = $project->trello_board_id;

        if ($trelloBoardId) {
            $this->fetchTrelloCards($trelloBoardId);
        }
    }

    public function fetchTrelloCards($boardId)
    {
        $trelloService = app(TrelloService::class);
        $this->trelloCards = $trelloService->fetchTrelloCards($boardId);
    }

    public function markAsDone($cardId, $checkItemId)
    {
        // Implement the logic to mark the checklist item as done
        // You can call your TrelloService to update the card status
    }

    public function getChecklistItems($card)
    {
        // Prepare the checklist items for the Filament table
        $items = [];
        if (!empty($card['checklist'])) {
            foreach ($card['checklist'] as $checklist) {
                foreach ($checklist['checkItems'] as $checkItem) {
                    $items[] = [
                        'id' => $checkItem['id'], // assuming you have an ID to identify each item
                        'name' => $checkItem['name'],
                        'state' => $checkItem['state'],
                        'due' => $checkItem['due'] ? date('Y-m-d', strtotime($checkItem['due'])) : 'No deadline',
                    ];
                }
            }
        }
        return $items;
    }
}
