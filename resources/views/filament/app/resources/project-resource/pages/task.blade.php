<x-filament::page>
    <h1>Task List from Trello</h1>

    @if ($trelloCards)
        @foreach ($trelloCards as $card)
            @php
                $checklistItems = $this->getChecklistItems($card);
                $dueDate = $card['due'] ? date('Y-m-d', strtotime($card['due'])) : 'N/A';
            @endphp
            
            <x-checklist-table 
                :cardName="$card['name']"
                :dueDate="$dueDate"
                :checklistItems="$checklistItems"
                :cardId="$card['id']" />
        @endforeach
    @else
        <p>No cards found for the "Departments" list.</p>
    @endif
</x-filament::page>
