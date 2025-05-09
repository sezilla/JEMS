<div>
    @if($loading)
        <div class="flex flex-col items-center justify-center min-h-[400px] space-y-4">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500"></div>
            <p class="text-gray-600 dark:text-gray-400">Loading tasks...</p>
        </div>
    @else
        <div wire:poll.10s>
            @if (!empty($trelloCards))
                @foreach ($trelloCards as $card)
                    <x-filament::section class="mb-8" wire:key="card-{{ $card['id'] }}">
                        <header class="flex items-center justify-between mb-4">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $card['name'] }}</h2>
                            <span class="text-sm text-gray-600 dark:text-gray-400 flex gap-4">
                                {{ $card['due'] ? \Carbon\Carbon::parse($card['due'])->format('F d, Y') : 'No Due Date' }}
                                @if (auth()->user()->hasRole('Coordinator'))
                                    <x-filament::modal id="set-card-due" wire:key="modal-card-due-{{ $card['id'] }}">
                                        <x-slot name="trigger">
                                            <x-filament::icon-button icon="heroicon-o-ellipsis-vertical"
                                                x-on:click="$wire.setCurrentTask({
                                                    card_id: '{{ $card['id'] }}',
                                                    due_date: '{{ $card['due'] ?? '' }}',
                                                })" />
                                        </x-slot>
                                        <p class="text-gray-800 dark:text-gray-200">Set Department Due</p>
                                        <x-filament::input.wrapper>
                                            <x-filament::input type="date" wire:model.defer="currentTask.due_date" />
                                        </x-filament::input.wrapper>
                                        <div class="flex justify-end space-x-3">
                                            <x-filament::button color="primary" wire:click="setDepartmentDue"
                                                x-on:click="if (await $wire.setDepartmentDue()) { $dispatch('close-modal', { id: 'set-card-due' }); $wire.$refresh(); }">
                                                Save
                                            </x-filament::button>
                                        </div>
                                    </x-filament::modal>
                                @endif
                            </span>
                        </header>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($card['checklists'] ?? [] as $checklist)
                                <x-filament::card wire:key="checklist-{{ $checklist['id'] }}">
                                    <header class="mb-2 flex justify-between items-center">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                            {{ $checklist['name'] }}
                                        </h3>
                                        <div class="flex gap-2">
                                            @if (auth()->user()->hasRole('Coordinator'))
                                                <x-filament::modal id="add-checklist-item-modal-{{ $checklist['id'] }}"
                                                    wire:key="modal-add-{{ $checklist['id'] }}">
                                                    <x-slot name="trigger">
                                                        <x-filament::icon-button icon="heroicon-o-plus"
                                                            x-on:click="$wire.setCurrentTask({
                                                            checklist_id: '{{ $checklist['id'] }}',
                                                            card_id: '{{ $card['id'] }}',
                                                            item_id: null,
                                                            name: '',
                                                            due_date: null,
                                                            state: 'incomplete',
                                                            user_id: null
                                                        })" />
                                                    </x-slot>

                                                    <p class="text-gray-800 dark:text-gray-200">Add Task</p>

                                                    <x-filament::input.wrapper>
                                                        <x-filament::input type="text"
                                                            wire:model.defer="currentTask.name" label="Task Name"
                                                            required />
                                                    </x-filament::input.wrapper>

                                                    <x-filament::input.wrapper>
                                                        <x-filament::input type="date"
                                                            wire:model.defer="currentTask.due_date" label="Due Date" />
                                                    </x-filament::input.wrapper>

                                                    <x-filament::input.wrapper>
                                                        <x-filament::input.select wire:model.defer="currentTask.user_id"
                                                            id="user-select">
                                                            <option value="">Select User</option>
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}">
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </x-filament::input.select>
                                                        @if (app()->environment('local'))
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                Selected user_id: {{ $currentTask['user_id'] ?? 'null' }}
                                                            </div>
                                                        @endif
                                                    </x-filament::input.wrapper>

                                                    <div class="flex justify-end space-x-3">
                                                        <x-filament::button color="primary" wire:click="createTask"
                                                            x-on:click="$dispatch('close-modal'); $wire.$refresh()"> Add
                                                        </x-filament::button>
                                                    </div>
                                                </x-filament::modal>
                                            @endif
                                            {{-- <x-filament::icon-button icon="heroicon-o-ellipsis-vertical" /> --}}
                                        </div>
                                    </header>
                                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($checklist['items'] ?? [] as $item)
                                            <li class="py-3 flex items-center justify-between"
                                                wire:key="checklist-item-{{ $item['id'] }}">
                                                <div>
                                                    <div class="flex gap-4">
                                                        <p class="font-semibold text-gray-800 dark:text-gray-200">
                                                            {{ $item['name'] }}
                                                        </p>
                                                        <!-- Edit check item modal -->
                                                        <x-filament::modal id="edit-label-modal-{{ $item['id'] }}"
                                                            wire:key="modal-edit-{{ $item['id'] }}">
                                                            <x-slot name="trigger">
                                                                <x-filament::icon-button icon="heroicon-m-pencil-square"
                                                                    x-on:click="$wire.setCurrentTask({
                                                                        card_id: '{{ $card['id'] }}',
                                                                        checklist_id: '{{ $checklist['id'] }}',
                                                                        item_id: '{{ $item['id'] }}',
                                                                        name: '{{ $item['name'] }}',
                                                                        due_date: '{{ $item['due_date'] ?? '' }}',
                                                                        state: '{{ $item['state'] ?? 'incomplete' }}',
                                                                        user_id: '{{ $item['user_id'] ?? '' }}'
                                                                    })" />
                                                            </x-slot>

                                                            <div class="space-y-6">
                                                                <div class="flex items-center justify-between">
                                                                    <p class="text-gray-800 dark:text-gray-200">
                                                                        Edit Task
                                                                    </p>
                                                                    @if (auth()->user()->hasRole('Coordinator'))
                                                                        <x-filament::modal
                                                                            id="delete-task-modal-{{ $item['id'] }}"
                                                                            wire:key="modal-delete-{{ $item['id'] }}">
                                                                            <x-slot name="trigger">
                                                                                <x-filament::icon-button
                                                                                    icon="heroicon-o-trash" color="danger"
                                                                                    x-on:click="$dispatch('close-modal'); $wire.setCurrentTask({
                                                                                    checklist_id: '{{ $checklist['id'] }}',
                                                                                    item_id: '{{ $item['id'] }}'
                                                                                });" />

                                                                            </x-slot>
                                                                            <div
                                                                                class="flex flex-col items-center justify-center gap-2">
                                                                                <x-filament::icon
                                                                                    icon="heroicon-o-exclamation-triangle"
                                                                                    class="text-red-500"
                                                                                    style="width: 34px; height: 34px;" />
                                                                                <p
                                                                                    class="text-gray-800 dark:text-gray-200 text-center">
                                                                                    Are you sure you want to delete this
                                                                                    task?
                                                                                </p>
                                                                            </div>
                                                                            <div class="flex justify-end space-x-3">
                                                                                <x-filament::button color="danger"
                                                                                    wire:click="deleteTask"
                                                                                    x-on:click="if (await $wire.deleteTask()) { $dispatch('close-modal', { id: 'delete-task-modal-{{ $item['id'] }}' }); $wire.$refresh(); }">
                                                                                    Delete
                                                                                </x-filament::button>
                                                                            </div>
                                                                        </x-filament::modal>
                                                                    @endif
                                                                </div>

                                                                <x-filament::input.wrapper>
                                                                    <x-filament::input type="text"
                                                                        wire:model.defer="currentTask.name"
                                                                        label="Task Name" :disabled="!auth()->user()->hasRole('Coordinator')" />
                                                                </x-filament::input.wrapper>
                                                                @if (auth()->user()->hasRole('Coordinator'))
                                                                    <x-filament::input.wrapper>
                                                                        <x-filament::input type="date"
                                                                            wire:model.defer="currentTask.due_date"
                                                                            label="Due Date" />
                                                                    </x-filament::input.wrapper>
                                                                @endif
                                                                <x-filament::input.wrapper>
                                                                    <x-filament::input.select wire:model="user_id"
                                                                        id="user-select" :disabled="!auth()
                                                                            ->user()
                                                                            ->hasAnyRole(['Coordinator', 'Team Leader'])">
                                                                        <option value="">Select User</option>
                                                                        @foreach ($users as $user)
                                                                            <option value="{{ $user->id }}">
                                                                                {{ $user->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </x-filament::input.select>
                                                                    <!-- Debug output during development -->
                                                                    @if (app()->environment('local'))
                                                                        <div class="text-xs text-gray-500 mt-1">
                                                                            Selected user_id: {{ $user_id ?? 'null' }}
                                                                        </div>
                                                                    @endif
                                                                </x-filament::input.wrapper>
                                                                <label class="py-2 flex gap-2">
                                                                    <x-filament::input.checkbox label="Is Completed"
                                                                        wire:model="currentTask.state" value="complete"
                                                                        :checked="strtolower($item['state'] ?? '') === 'complete'" :unchecked="strtolower($item['state'] ?? '') === 'incomplete'" />
                                                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                                                        Is Completed
                                                                    </span>
                                                                </label>
                                                                <div class="flex justify-end space-x-3">
                                                                    <x-filament::button color="primary"
                                                                        wire:click="saveEditTask"
                                                                        wire:loading.attr="disabled"
                                                                        x-on:click="$dispatch('close-modal'); $wire.$refresh()">
                                                                        Save
                                                                    </x-filament::button>
                                                                </div>
                                                            </div>
                                                        </x-filament::modal>
                                                    </div>

                                                    <div class="flex gap-2 p-1">
                                                        <!-- Due date modal -->
                                                        <x-filament::modal id="set-due-date-modal-{{ $item['id'] }}"
                                                            wire:key="modal-due-{{ $item['id'] }}">
                                                            <x-slot name="trigger">
                                                                <x-filament::icon-button icon="heroicon-m-calendar"
                                                                    x-on:click="$wire.setCurrentTask({
                                                                        ...{{ json_encode($item) }},
                                                                        card_id: '{{ $card['id'] }}',
                                                                        checklist_id: '{{ $checklist['id'] }}',
                                                                        item_id: '{{ $item['id'] }}'
                                                                    })" />
                                                            </x-slot>
                                                            <p>
                                                                Due:
                                                                <span>
                                                                    {{ $item['due'] ? \Carbon\Carbon::parse($item['due'])->format('F d, Y') : 'No Due Date' }}
                                                                </span>
                                                            </p>
                                                            <x-filament::input.wrapper>
                                                                <x-filament::input type="date"
                                                                    wire:model.defer="dueDate" :disabled="!auth()->user()->hasRole('Coordinator')" />
                                                            </x-filament::input.wrapper>
                                                            <div class="flex justify-end space-x-3">
                                                                @if (auth()->user()->hasRole('Coordinator'))
                                                                    <x-filament::button color="primary"
                                                                        wire:click="saveDueDate"
                                                                        x-on:click="if (await $wire.saveDueDate()) { $dispatch('close-modal', { id: 'set-due-date-modal-{{ $item['id'] }}' }); $wire.$refresh(); }">
                                                                        Save
                                                                    </x-filament::button>
                                                                @endif
                                                            </div>
                                                        </x-filament::modal>

                                                        <!-- Display due date -->
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ !empty($item['due']) ? \Carbon\Carbon::parse($item['due'])->format('F d, Y') : 'No Due Date' }}
                                                        </p>
                                                    </div>

                                                    <div class="flex gap-2 p-1">
                                                        <!-- Assigned user modal -->
                                                        <x-filament::modal id="set-user-modal-{{ $item['id'] }}"
                                                            wire:key="modal-user-{{ $item['id'] }}">
                                                            <x-slot name="trigger">
                                                                <x-filament::icon-button icon="heroicon-m-user"
                                                                    x-on:click="$wire.setCurrentTask({
                                                                        ...{{ json_encode($item) }},
                                                                        card_id: '{{ $card['id'] }}',
                                                                        checklist_id: '{{ $checklist['id'] }}',
                                                                        item_id: '{{ $item['id'] }}'
                                                                    })" />
                                                            </x-slot>
                                                            <p>
                                                                Assigned to:
                                                                <span>
                                                                    @if (!empty($item['user_id']))
                                                                        {{ \App\Models\User::find($item['user_id'])->name ?? 'Unknown' }}
                                                                    @else
                                                                        No Assigned User
                                                                    @endif
                                                                </span>
                                                            </p>
                                                            <x-filament::input.wrapper>
                                                                <x-filament::input.select wire:model="user_id"
                                                                    id="user-select" :disabled="!auth()
                                                                        ->user()
                                                                        ->hasAnyRole(['Coordinator', 'Team Leader'])">
                                                                    <option value="">Select User</option>
                                                                    @foreach ($users as $user)
                                                                        <option value="{{ $user->id }}">
                                                                            {{ $user->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </x-filament::input.select>
                                                                <!-- Debug output during development -->
                                                                @if (app()->environment('local'))
                                                                    <div class="text-xs text-gray-500 mt-1">
                                                                        Selected user_id: {{ $user_id ?? 'null' }}
                                                                    </div>
                                                                @endif
                                                            </x-filament::input.wrapper>
                                                            <div class="flex justify-end space-x-3">
                                                                @if (auth()->user()->hasAnyRole(['Coordinator', 'Team Leader']))
                                                                    <x-filament::button color="primary"
                                                                        wire:click="assignUserToCheckItem"
                                                                        x-on:click="$dispatch('close-modal'); $wire.$refresh()">                                                                    Save
                                                                    </x-filament::button>
                                                                @endif
                                                            </div>
                                                        </x-filament::modal>

                                                        <!-- Display assigned user -->
                                                        @if (!empty($item['user_id']))
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                {{ optional(\App\Models\User::find($item['user_id']))->name ?? 'Unknown' }}
                                                            </p>
                                                        @else
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                No Assigned User
                                                            </p>
                                                        @endif
                                                    </div>

                                                    <div class="mt-2 flex gap-2">
                                                        @if (strtolower($item['state'] ?? '') === 'complete')
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

                                                <!-- Complete task modal -->
                                                <x-filament::modal id="complete-task-modal-{{ $item['id'] }}"
                                                    wire:key="modal-complete-{{ $item['id'] }}">
                                                    <x-slot name="trigger">
                                                        @if ($item['state'] === 'complete')
                                                            {{-- Mark as incomplete --}}
                                                            <x-filament::icon-button icon="heroicon-m-x-mark"
                                                                x-on:click="$wire.setCurrentTask({
                                                                    ...{{ json_encode($item) }},
                                                                    card_id: '{{ $card['id'] }}',
                                                                    checklist_id: '{{ $checklist['id'] }}',
                                                                    item_id: '{{ $item['id'] }}',
                                                                    desired_state: 'incomplete'
                                                                })"
                                                                color="danger" label="Mark as Incomplete" />
                                                        @else
                                                            {{-- Mark as complete --}}
                                                            <x-filament::icon-button icon="heroicon-m-check"
                                                                x-on:click="$wire.setCurrentTask({
                                                                    ...{{ json_encode($item) }},
                                                                    card_id: '{{ $card['id'] }}',
                                                                    checklist_id: '{{ $checklist['id'] }}',
                                                                    item_id: '{{ $item['id'] }}',
                                                                    desired_state: 'complete'
                                                                })"
                                                                color="primary" label="Mark as Complete" />
                                                        @endif

                                                    </x-slot>

                                                    <p class="text-gray-800 dark:text-gray-200">
                                                        @if ($item['state'] === 'complete')
                                                            Are you sure you want to mark this task as
                                                            <strong>incomplete</strong>?
                                                        @else
                                                            Are you sure you want to mark this task as
                                                            <strong>complete</strong>?
                                                        @endif
                                                    </p>

                                                    <div class="flex justify-end space-x-3">
                                                        <x-filament::button color="primary"
                                                            wire:click="updateCheckItemState"
                                                            x-on:click="$dispatch('close-modal'); $wire.$refresh()">
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
                <div class="flex flex-col items-center justify-center p-12 text-center">
                    <div class="mb-4">
                        <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-12 h-12 text-gray-400" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Tasks Found</h3>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md">
                        There are no tasks available for this project yet. Tasks will appear here once they are created.
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
