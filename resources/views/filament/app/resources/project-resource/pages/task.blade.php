<x-filament::page>
    <x-filament::section>
        <x-slot name="header">
            <h1 class="text-2xl font-bold">Task List from Trello</h1>
        </x-slot>

        @php
            $uniqueCards = collect($trelloCards)->unique('id')->toArray();
        @endphp

        @if ($uniqueCards && count($uniqueCards) > 0)
            <div class="space-y-4">
                @foreach ($uniqueCards as $card)
                    @php
                        $checklistItems = $this->getChecklistItems($card);
                        $dueDate = $card['due'] ? date('Y-m-d', strtotime($card['due'])) : 'N/A';
                    @endphp

                    <x-filament::card>
                        <x-checklist-table 
                        :checklistItems="$checklistItems"
                        :cardId="$card['id']">
                        <x-filament::badge 
                            color="{{ $dueDate !== 'N/A' && strtotime($dueDate) < time() ? 'danger' : 'success' }}">
                            {{ $dueDate !== 'N/A' && strtotime($dueDate) < time() ? 'Overdue' : 'On Track' }}
                        </x-filament::badge>
                        <x-slot name="cardName">{{ $card['name'] }}</x-slot>
                        <x-slot name="dueDate">{{ $dueDate }}</x-slot>
                        </x-checklist-table>
                    </x-filament::card>
                @endforeach
            </div>
        @else
            <div class="text-center">
                <p class="text-gray-500">No cards found for the "Departments" list.</p>
            </div>
        @endif
    </x-filament::section>
</x-filament::page>
