<div class="mb-6">
    <h2 class="text-lg font-bold">{{ $cardName }}</h2>
    <p><strong>Due Date:</strong> {{ $dueDate }}</p>

    <x-filament-tables::table>
        <thead>
            <tr>
                <th>Task</th>
                <th>Due</th>
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
                            wire:click="markAsDone('{{ $cardId }}', '{{ $item['id'] }}')"
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
