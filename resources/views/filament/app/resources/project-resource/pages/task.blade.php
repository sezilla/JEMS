<x-filament::page>
    <div wire:poll.10s>
        @if ($trelloCards && count($trelloCards))
            @foreach ($trelloCards as $card)
                <x-filament::section class="mb-8" wire:key="card-{{ $card['id'] }}">
                    <header class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $card['name'] }}</h2>
                        <span class="text-sm text-gray-600 dark:text-gray-400 flex gap-4">
                            {{ $card['due'] ? \Carbon\Carbon::parse($card['due'])->format('F d, Y') : 'No Due Date' }}
                            <x-filament::icon-button icon="heroicon-o-ellipsis-vertical" wire:click="" />
                        </span>
                    </header>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($card['checklists'] as $checklist)
                            <x-filament::card wire:key="checklist-{{ $checklist['id'] }}">
                                <header class="mb-2 flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        {{ $checklist['name'] }}
                                    </h3>
                                    <div class="flex gap-2">
                                        <x-filament::modal id="add-checklist-item-modal-{{ $checklist['id'] }}"
                                            :wire:key="'modal-'.$checklist['id']">
                                            <x-slot name="trigger">
                                                <x-filament::icon-button icon="heroicon-o-plus"
                                                    wire:click="setCurrentTask({{ json_encode([
                                                        'checklist_id' => $checklist['id'],
                                                        'card_id' => $card['id'],
                                                        'item_id' => null,
                                                        'name' => '',
                                                        'due_date' => null,
                                                        'state' => 'incomplete',
                                                    ]) }})" />
                                            </x-slot>

                                            <p class="text-gray-800 dark:text-gray-200">
                                                Add Task
                                            </p>

                                            <x-filament::input.wrapper>
                                                <x-filament::input type="text" wire:model.defer="currentTask.name"
                                                    label="Task Name" />
                                            </x-filament::input.wrapper>

                                            <x-filament::input.wrapper>
                                                <x-filament::input type="date"
                                                    wire:model.defer="currentTask.due_date" label="Due Date" />
                                            </x-filament::input.wrapper>

                                            <div class="flex justify-end space-x-3">
                                                <x-filament::button color="primary" wire:click="createTask">
                                                    Add
                                                </x-filament::button>
                                            </div>
                                        </x-filament::modal>
                                        <x-filament::icon-button icon="heroicon-o-ellipsis-vertical" />
                                    </div>
                                </header>
                                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($checklist['items'] as $item)
                                        <li class="py-3 flex items-center justify-between"
                                            wire:key="checklist-item-{{ $item['id'] }}">
                                            <div>
                                                <div class="flex gap-4">
                                                    <p class="font-semibold text-gray-800 dark:text-gray-200">
                                                        {{ $item['name'] }}
                                                    </p>
                                                    <!-- edit check item modal -->
                                                    <x-filament::modal id="edit-label-modal-{{ $item['id'] }}"
                                                        :wire:key="'modal-edit-'.$item['id']">
                                                        <x-slot name="trigger">
                                                            <x-filament::icon-button icon="heroicon-m-pencil-square"
                                                                wire:click="setCurrentTask({{ json_encode([
                                                                    'card_id' => $card['id'],
                                                                    'checklist_id' => $checklist['id'],
                                                                    'item_id' => $item['id'],
                                                                    'name' => $item['name'],
                                                                    'due_date' => $item['due_date'] ?? null,
                                                                    'state' => $item['state'] ?? 'incomplete',
                                                                ]) }})" />
                                                        </x-slot>

                                                        <div class="space-y-6">
                                                            <div class="flex items-center justify-between">
                                                                <p class="text-gray-800 dark:text-gray-200">
                                                                    Edit Task
                                                                </p>
                                                                <x-filament::modal
                                                                    id="delete-task-modal-{{ $item['id'] }}"
                                                                    :wire:key="'modal-delete-'.$item['id']">
                                                                    <x-slot name="trigger">
                                                                        <x-filament::icon-button icon="heroicon-o-trash"
                                                                            color="danger"
                                                                            wire:click="setCurrentTask({{ json_encode([
                                                                                'checklist_id' => $checklist['id'],
                                                                                'item_id' => $item['id'],
                                                                            ]) }})" />
                                                                    </x-slot>
                                                                    <div
                                                                        class="flex flex-col items-center justify-center gap-2">
                                                                        <x-filament::icon
                                                                            icon="heroicon-o-exclamation-triangle"
                                                                            class="text-red-500"
                                                                            style="width: 34px; height: 34px;" />
                                                                        <p
                                                                            class="text-gray-800 dark:text-gray-200 text-center">
                                                                            Are you sure you want to delete this task?
                                                                        </p>
                                                                    </div>
                                                                    <div class="flex justify-end space-x-3">
                                                                        <x-filament::button color="danger"
                                                                            wire:click="deleteTask">
                                                                            Delete
                                                                        </x-filament::button>
                                                                    </div>
                                                                </x-filament::modal>
                                                            </div>

                                                            <x-filament::input.wrapper>
                                                                <x-filament::input type="text"
                                                                    wire:model.defer="currentTask.name"
                                                                    label="Task Name" />
                                                            </x-filament::input.wrapper>
                                                            <x-filament::input.wrapper>
                                                                <x-filament::input type="date"
                                                                    wire:model.defer="currentTask.due_date"
                                                                    label="Task Name" />
                                                            </x-filament::input.wrapper>
                                                            <label class="py-2 flex gap-2">
                                                                <x-filament::input.checkbox label="Is Completed"
                                                                    wire:model="currentTask.state" value="complete"
                                                                    :checked="strtolower($item['state']) === 'complete'" :unchecked="strtolower($item['state']) === 'incomplete'" />
                                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                                    Is Completed
                                                                </span>
                                                            </label>
                                                            <div class="flex justify-end space-x-3">
                                                                <x-filament::button color="primary"
                                                                    wire:click="saveEditTask"
                                                                    wire:loading.attr="disabled">
                                                                    Save
                                                                </x-filament::button>
                                                            </div>
                                                        </div>
                                                    </x-filament::modal>

                                                </div>
                                                <div class="flex gap-2 p-1">
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
                                                        <p>
                                                            Due:
                                                            <span>
                                                                {{ $item['due'] ? \Carbon\Carbon::parse($item['due'])->format('F d, Y') : 'No Due Date' }}
                                                            </span>
                                                        </p>
                                                        <x-filament::input.wrapper>
                                                            <x-filament::input type="date"
                                                                wire:model.defer="dueDate" />
                                                        </x-filament::input.wrapper>
                                                        <div class="flex justify-end space-x-3">
                                                            <x-filament::button color="primary"
                                                                wire:click="saveDueDate">
                                                                Save
                                                            </x-filament::button>
                                                        </div>
                                                    </x-filament::modal>

                                                    <!-- Display due date -->
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        <span>
                                                            {{ $item['due'] ? \Carbon\Carbon::parse($item['due'])->format('F d, Y') : '' }}
                                                        </span>
                                                    </p>
                                                </div>
                                                <div class="flex gap-2 p-1">

                                                    {{-- assigned user modal --}}
                                                    <x-filament::modal id="set-user-modal-{{ $item['id'] }}"
                                                        :wire:key="'modal-'.$item['id']">
                                                        <x-slot name="trigger">
                                                            <x-filament::icon-button icon="heroicon-m-user"
                                                                wire:click="setCurrentTask({{ json_encode(
                                                                    array_merge($item, [
                                                                        'card_id' => $card['id'],
                                                                        'checklist_id' => $checklist['id'],
                                                                        'item_id' => $item['id'],
                                                                    ]),
                                                                ) }})" />
                                                        </x-slot>
                                                        <p>
                                                            Assigned to:
                                                            <span>
                                                                {{ $item['user_id'] ? \App\Models\User::find($item['user_id'])->name : 'No Assigned User' }}
                                                            </span>
                                                        </p>
                                                        <x-filament::input.wrapper>
                                                            <x-filament::input.select wire:model.defer="user_id">
                                                                <option value="">Select User</option>
                                                                @foreach ($users as $user)
                                                                    <option value="{{ $user->id }}">
                                                                        {{ $user->name }}
                                                                    </option>
                                                                @endforeach
                                                            </x-filament::input.select>
                                                        </x-filament::input.wrapper>
                                                        <div class="flex justify-end space-x-3">
                                                            <x-filament::button color="primary"
                                                                wire:click="assignUserToCheckItem">
                                                                Save
                                                            </x-filament::button>
                                                        </div>
                                                    </x-filament::modal>

                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        <span>
                                                            {{ $item['user_id'] ?? 'No User Assigned' }}
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

                                            <!-- complete modal -->
                                            <x-filament::modal id="edit-label-modal-{{ $item['id'] }}"
                                                :wire:key="'modal-edit-'.$item['id']">
                                                <x-slot name="trigger">
                                                    <x-filament::icon-button icon="heroicon-m-check"
                                                        wire:click="setCurrentTask({{ json_encode(
                                                            array_merge($item, [
                                                                'card_id' => $card['id'],
                                                                'checklist_id' => $checklist['id'],
                                                                'item_id' => $item['id'],
                                                            ]),
                                                        ) }})" />
                                                </x-slot>
                                                <p class="text-gray-800 dark:text-gray-200">
                                                    Are you sure you want to mark this task as complete?
                                                </p>
                                                <div class="flex justify-end space-x-3">
                                                    <x-filament::button color="primary"
                                                        wire:click="updateCheckItemState">
                                                        Submit
                                                    </x-filament::button>
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
    </div>
</x-filament::page>
