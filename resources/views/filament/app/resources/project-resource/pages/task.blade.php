<x-filament::page>
    @if ($trelloCards && count($trelloCards))
        @foreach ($trelloCards as $card)
            <!-- Each department card becomes its own section -->
            <x-filament::section class="mb-8">
                <header class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold">
                        {{ $card['name'] }}
                    </h2>
                    <span class="text-sm text-gray-600">
                        {{ $card['due'] ? \Carbon\Carbon::parse($card['due'])->format('M d, Y') : 'No Due Date' }}
                    </span>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if (!empty($card['checklists']))
                        @foreach ($card['checklists'] as $checklist)
                            <x-filament::card>
                                <header class="mb-2">
                                    <h3 class="text-lg font-bold">{{ $checklist['name'] }}</h3>
                                </header>
                                @if (!empty($checklist['items']))
                                    <ul class="divide-y divide-gray-200">
                                        @foreach ($checklist['items'] as $item)
                                            <li class="py-2 flex items-center justify-between">
                                                <span>{{ $item['name'] }}</span>
                                                @if (isset($item['state']) && strcasecmp($item['state'], 'complete') === 0)
                                                    <span class="text-green-600 font-bold">&#10003;</span>
                                                @else
                                                    <span class="text-red-600 font-bold">&#10007;</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-500">No tasks in this checklist.</p>
                                @endif
                            </x-filament::card>
                        @endforeach
                    @else
                        <div class="text-gray-600">No checklists available for this department.</div>
                    @endif
                </div>
            </x-filament::section>
        @endforeach
    @else
        <div class="p-6 text-gray-600">No tasks found.</div>
    @endif
</x-filament::page>
