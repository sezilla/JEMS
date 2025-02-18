<x-filament::page>
    <x-filament::section>
        <x-slot name="header">
            <h1 class="text-2xl font-bold">Task Checklist</h1>
        </x-slot>

        @php
            $uniqueCards = collect($trelloCards)->unique('id')->toArray();
        @endphp

        @if ($uniqueCards && count($uniqueCards) > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6"> 
                @foreach ($uniqueCards as $card)
                    @php
                        $checklistItems = $this->getChecklistItems($card);
                        $dueDate = $card['due'] ? date('Y-m-d', strtotime($card['due'])) : 'No deadline';
                    @endphp

                    <div class="border rounded-lg shadow-sm p-4 bg-white">
                        <h2 class="text-lg font-semibold mb-3">{{ $card['name'] }}</h2>
                        
                        <ul class="space-y-3">
                            @foreach ($checklistItems as $item)
                                <li class="border-b pb-3">
                                    <div class="flex items-center justify-between gap-8"> <!-- Added gap here -->
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" 
                                                class="form-checkbox h-5 w-5 text-blue-500"
                                                wire:click="markAsDone('{{ $card['id'] }}', '{{ $item['id'] }}')">
                                            
                                            <span class="font-medium {{ $item['state'] == 'complete' ? 'line-through text-gray-400' : '' }}">
                                                {{ $item['name'] }}
                                            </span>
                                        </div>
                                        <div class="text-right min-w-[100px]"> <!-- Ensures spacing -->
                                            <p class="text-gray-500 text-xs uppercase font-semibold">Due Date</p>
                                            <span class="text-gray-700 text-sm">
                                                {{ $item['due'] ?? 'No deadline' }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center">
                <p class="text-gray-500">No checklist items found.</p>
            </div>
        @endif
    </x-filament::section>
</x-filament::page>
