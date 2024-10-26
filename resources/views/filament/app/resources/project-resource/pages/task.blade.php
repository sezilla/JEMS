<x-filament::page>
    <h1>Task List from Trello</h1>

    @if ($trelloCards)
        @foreach ($trelloCards as $card)
            <div class="mb-6">
                <h2 class="text-lg font-bold">{{ $card['name'] }}</h2>
                <p><strong>Due Date:</strong> {{ $card['due'] ? date('Y-m-d', strtotime($card['due'])) : 'No deadline' }}</p>

                @php
                    $checklistItems = $this->getChecklistItems($card);
                @endphp

                <x-filament-tables::table>
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($checklistItems as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['due'] }}</td>
                                <td>
                                    <x-filament::button
                                        wire:click="markAsDone('{{ $card['id'] }}', '{{ $item['id'] }}')"
                                        color="success"
                                        size="sm">
                                        Done
                                    </x-filament::button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-filament-tables::table>
            </div>
        @endforeach
    @else
        <p>No cards found for the "Departments" list.</p>
    @endif
</x-filament::page>
