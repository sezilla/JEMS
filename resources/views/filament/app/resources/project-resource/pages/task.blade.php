<x-filament::page>
    @if ($trelloCards && count($trelloCards))
        @foreach ($trelloCards as $card)
            <x-filament::section class="mb-8">
                <header class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $card['name'] }}</h2>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $card['due'] ? \Carbon\Carbon::parse($card['due'])->format('F d, Y') : 'No Due Date' }}
                    </span>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($card['checklists'] as $checklist)
                        <x-filament::card>
                            <header class="mb-2 flex justify-between items-center">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $checklist['name'] }}
                                </h3>
                            </header>
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($checklist['items'] as $item)
                                    <li class="py-3 flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-gray-200">
                                                {{ $item['name'] }}
                                            </p>
                                            <div class="flex items-center gap-2">
                                                <!-- Due date modal -->
                                                <x-filament::modal id="set-due-date-modal-{{ $item['id'] }}"
                                                    :wire:key="'modal-'.$item['id']">
                                                    <x-slot name="trigger">
                                                        <x-filament::icon-button icon="heroicon-m-calendar"
                                                            wire:click="setCurrentTask({{ json_encode(
                                                                array_merge($item, [
                                                                    'card_id' => $card['id'],
                                                                    'checklist_id' => $checklist['id'],
                                                                    'item_id' => $item['id'],
                                                                ]),
                                                            ) }})" />
                                                    </x-slot>
                                                    <!-- Display current due date -->
                                                    <p>
                                                        Due:
                                                        <span>
                                                            {{ $item['due'] ? \Carbon\Carbon::parse($item['due'])->format('F d, Y') : 'No Due Date' }}
                                                        </span>
                                                    </p>
                                                    <x-filament::input.wrapper>
                                                        <x-filament::input type="date" wire:model.defer="dueDate" />
                                                    </x-filament::input.wrapper>
                                                    <div class="flex justify-end space-x-3">
                                                        <x-filament::button color="primary" wire:click="saveDueDate">
                                                            Save
                                                        </x-filament::button>
                                                    </div>
                                                </x-filament::modal>
                                                <!-- Display due date outside the modal -->
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    <span>
                                                        {{ $item['due'] ? \Carbon\Carbon::parse($item['due'])->format('F d, Y') : '' }}
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="mt-2 flex gap-2">
                                                @if (strtolower($item['state']) === 'complete')
                                                    <x-filament::badge color="success" class="px-3 py-1 text-sm"
                                                        icon="heroicon-o-check-circle">
                                                        Complete
                                                    </x-filament::badge>
                                                @else
                                                    <x-filament::badge color="warning" class="px-3 py-1 text-sm"
                                                        icon="heroicon-o-x-circle">
                                                        Incomplete
                                                    </x-filament::badge>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Additional modal (for example, editing a label) -->
                                        <x-filament::modal id="edit-label-modal-{{ $item['id'] }}"
                                            :wire:key="'modal-edit-'.$item['id']">
                                            <x-slot name="trigger">
                                                <x-filament::icon-button icon="heroicon-m-pencil-square"
                                                    label="New label" />
                                            </x-slot>
                                            <div class="space-y-6">
                                                <x-filament::input.wrapper>
                                                    <x-filament::input type="text" wire:model.defer="name" />
                                                </x-filament::input.wrapper>
                                                <x-filament::input.wrapper>
                                                    <x-filament::input.select wire:model.defer="status">
                                                        <option value="incomplete">Incomplete</option>
                                                        <option value="completed">Completed</option>
                                                    </x-filament::input.select>
                                                </x-filament::input.wrapper>
                                                <!-- Modal Actions -->
                                                <div class="flex justify-end space-x-3">
                                                    <x-filament::button color="primary" wire:click="saveLabel">
                                                        Save
                                                    </x-filament::button>
                                                </div>
                                            </div>
                                        </x-filament::modal>
                                    </li>
                                @endforeach
                            </ul>
                        </x-filament::card>
                    @endforeach
                </div>
            </x-filament::section>
        @endforeach
    @else
        <div class="p-6 text-gray-600 dark:text-gray-400">No tasks found.</div>
    @endif
</x-filament::page>
