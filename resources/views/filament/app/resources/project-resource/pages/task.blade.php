<x-filament::page>
    <h1>Task List from Trello</h1>

    @if ($trelloCards && count($trelloCards) > 0)
        @foreach ($trelloCards as $card)
            @php
                $checklistItems = $this->getChecklistItems($card);
                $dueDate = $card['due'] ? date('Y-m-d', strtotime($card['due'])) : 'N/A';
            @endphp
            
            <x-filament::section>
                <h2>{{ $card['name'] }}</h2>
                <p>Due Date: {{ $dueDate }}</p>
                
                @if ($checklistItems && count($checklistItems) > 0)
                    <ul>
                        @foreach ($checklistItems as $item)
                            <li>{{ $item['name'] }} - {{ $item['state'] === 'complete' ? 'Completed' : 'Pending' }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>No checklist items found.</p>
                @endif
            </x-filament::section>
        @endforeach
    @else
        <p>No cards found for the "Departments" list.</p>
    @endif
</x-filament::page>
