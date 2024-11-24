<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use App\Services\TrelloService;


class TrelloTask extends Component implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public $listId = 'your-departments-list-id'; // Replace with the actual Trello "Departments" list ID
    protected $trelloService;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->trelloService = app(TrelloService::class);
    }

    public function fetchChecklistItems()
    {
        $cards = $this->trelloService->getListCards($this->listId);

        $checklistItems = [];
        foreach ($cards as $card) {
            $cardChecklists = $this->trelloService->getCardData($card['id'])['checklists'] ?? [];
            foreach ($cardChecklists as $checklist) {
                foreach ($checklist['checkItems'] as $item) {
                    $checklistItems[] = [
                        'card_name' => $card['name'],
                        'checklist_name' => $checklist['name'],
                        'item_name' => $item['name'],
                        'item_state' => $item['state'], // complete or incomplete
                    ];
                }
            }
        }

        return $checklistItems;
    }

    protected function getTableQuery()
    {
        // No query used here, as data is fetched dynamically.
        return [];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('card_name')->label('Card Name')->sortable(),
            TextColumn::make('checklist_name')->label('Checklist Name')->sortable(),
            TextColumn::make('item_name')->label('Item Name')->sortable(),
            TextColumn::make('item_state')->label('Status')->sortable(),
        ];
    }

    protected function getTableData(): array
    {
        return $this->fetchChecklistItems();
    }
    
    public function render()
    {
        return view('livewire.trello-task');
    }
}
