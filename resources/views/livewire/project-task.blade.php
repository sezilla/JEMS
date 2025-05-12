<div>
    @if ($loading)
        <div class="flex flex-col items-center justify-center min-h-[400px] space-y-4">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500"></div>
            <p class="text-gray-600 dark:text-gray-400">Loading tasks...</p>
        </div>
    @elseif($error)
        <div class="flex flex-col items-center justify-center p-12 text-center">
            <div class="mb-4">
                <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-12 h-12 text-danger-500" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Error Loading Tasks</h3>
            <p class="text-gray-600 dark:text-gray-400 max-w-md">
                {{ $error }}
            </p>
        </div>
    @else
        <div>
            <h1 class="text-2xl font-bold mb-4">Event Tasks</h1>

            @if (!empty($cards))
                @foreach ($cards as $card)
                    <x-filament::section class="my-4">
                        <header class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $card['card_name'] ?? 'Department Card' }}
                            </h2>
                            <div class="flex flex-row gap-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ !empty($card['card_due_date']) ? \Carbon\Carbon::parse($card['card_due_date'])->format('F d, Y') : 'No Due Date' }}
                                </p>
                                @if (auth()->user()->hasRole('Coordinator'))
                                    <x-filament::modal>
                                        <x-slot name="trigger">
                                            <x-filament::icon-button icon="heroicon-m-ellipsis-vertical"
                                                wire:click="openCardModal('{{ $card['card_id'] ?? '' }}')"
                                                tooltip="Edit Card Details"
                                                x-on:click="$wire.setCurrentCard({
                                                    card_id: '{{ $card['card_id'] ?? '' }}',
                                                    card_name: '{{ $card['card_name'] ?? '' }}',
                                                    card_due_date: '{{ $card['card_due_date'] ?? '' }}',
                                                    card_description: '{{ $card['card_description'] ?? '' }}'
                                                })" />
                                        </x-slot>

                                        <x-slot name="heading">
                                            <div class="flex items-center gap-2">
                                                <x-filament::icon icon="heroicon-o-document-text"
                                                    class="w-6 h-6 text-pink-500" />
                                                <span class="font-semibold text-lg">Edit Card Details</span>
                                            </div>
                                        </x-slot>

                                        <div class="space-y-2">
                                            <label for="due-date"
                                                class="text-sm font-medium text-gray-700 dark:text-gray-300">Due
                                                Date</label>
                                            <x-filament::input.wrapper>
                                                <x-filament::input type="date" wire:model.defer="cardDueDate"
                                                    value="{{ $card['card_due_date'] ?? '' }}" />
                                            </x-filament::input.wrapper>
                                            <span class="text-xs text-gray-400">Leave blank if no due date</span>
                                            @error('cardDueDate')
                                                <div class="text-xs text-danger-500">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="space-y-2">
                                            <label for="description"
                                                class="text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                            <x-filament::input.wrapper>
                                                <x-filament::input type="text" wire:model.defer="cardDescription"
                                                    value="{{ $card['card_description'] ?? '' }}" />
                                            </x-filament::input.wrapper>
                                            <span class="text-xs text-gray-400">Optional</span>
                                            @error('cardDescription')
                                                <div class="text-xs text-danger-500">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <x-slot name="footerActions">
                                            <div class="flex justify-end items-center gap-2">
                                                <x-filament::button wire:click="saveCard('{{ $currentCard }}')"
                                                    color="primary" icon="heroicon-o-check"
                                                    wire:loading.attr="disabled">
                                                    <span wire:loading.remove>Save Changes</span>
                                                    <span wire:loading class="flex items-center gap-1">
                                                        Saving...
                                                    </span>
                                                </x-filament::button>
                                            </div>
                                        </x-slot>
                                    </x-filament::modal>
                                @endif
                            </div>
                        </header>
                        @if ($card['card_description'])
                            <div class="py-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $card['card_description'] }}</p>
                            </div>
                        @endif
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($card['checklists'] ?? [] as $checklist)
                                <x-filament::card>
                                    <header class="mb-2 flex justify-between items-center">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                            {{ $checklist['checklist_name'] ?? 'Checklist' }}
                                        </h3>
                                        <x-filament::modal>
                                            <x-slot name="trigger">
                                                <x-filament::icon-button icon="heroicon-o-plus"
                                                    wire:click="openChecklistModal('{{ $card['card_id'] }}', '{{ $checklist['checklist_id'] }}', '{{ $card['card_name'] }}')"
                                                    tooltip="Create Task" />
                                            </x-slot>

                                            <x-slot name="heading">
                                                <div class="flex items-center gap-2">
                                                    <x-filament::icon icon="heroicon-o-pencil-square"
                                                        class="w-6 h-6 text-pink-500" />
                                                    <span class="font-semibold text-lg">Create Task</span>
                                                </div>
                                            </x-slot>

                                            <div class="space-y-2">
                                                <label for="task-name"
                                                    class="text-sm font-medium text-gray-700 dark:text-gray-300">Task
                                                    Name</label>
                                                <x-filament::input.wrapper>
                                                    <x-filament::input type="text"
                                                        wire:model.defer="currentCheckItemName" />
                                                </x-filament::input.wrapper>
                                                @error('currentCheckItemName')
                                                    <div class="text-xs text-danger-500">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="space-y-2">
                                                <label for="due-date"
                                                    class="text-sm font-medium text-gray-700 dark:text-gray-300">Due
                                                    Date</label>
                                                <x-filament::input.wrapper>
                                                    <x-filament::input type="date"
                                                        wire:model.defer="checkItemDueDate" />
                                                </x-filament::input.wrapper>
                                                @error('checkItemDueDate')
                                                    <div class="text-xs text-danger-500">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="space-y-2">
                                                <label for="user"
                                                    class="text-sm font-medium text-gray-700 dark:text-gray-300">Assign
                                                    To</label>
                                                <x-filament::input.wrapper>
                                                    <x-filament::input.select wire:model.defer="assignedUser">
                                                        <option value="">Select User</option>
                                                        @foreach ($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}
                                                            </option>
                                                        @endforeach
                                                    </x-filament::input.select>
                                                </x-filament::input.wrapper>
                                                @error('assignedUser')
                                                    <div class="text-xs text-danger-500">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <x-slot name="footerActions">
                                                <div class="flex justify-end items-center gap-2">
                                                    <x-filament::button
                                                        wire:click="createTask('{{ $checklist['checklist_id'] }}')"
                                                        wire:loading.attr="disabled">
                                                        <span wire:loading.remove>Create Task</span>
                                                        <span wire:loading class="flex items-center gap-1">
                                                            Creating...
                                                        </span>
                                                    </x-filament::button>
                                                </div>
                                            </x-slot>
                                        </x-filament::modal>
                                    </header>
                                    <ul class="divide-y divide-gray-700">
                                        @foreach ($checklist['check_items'] ?? [] as $item)
                                            <li class="py-3 flex items-center justify-between">
                                                <div class="w-full flex justify-between">
                                                    <div class="w-full">
                                                        <div class="flex gap-4">
                                                            <p class="font-semibold text-gray-800 dark:text-gray-200">
                                                                {{ $item['check_item_name'] ?? 'Task' }}
                                                            </p>
                                                            <x-filament::modal>
                                                                <x-slot name="trigger">
                                                                    <x-filament::icon-button
                                                                        icon="heroicon-o-pencil-square"
                                                                        wire:click="openChecklistModal('{{ $card['card_id'] }}', '{{ $checklist['checklist_id'] }}')"
                                                                        tooltip="Edit Task"
                                                                        x-on:click="$wire.setCurrentTask({
                                                                            check_item_id: '{{ $item['check_item_id'] ?? '' }}',
                                                                            check_item_name: '{{ $item['check_item_name'] ?? '' }}',
                                                                            due_date: '{{ $item['due_date'] ?? '' }}',
                                                                            user_id: '{{ $item['user_id'] ?? '' }}',
                                                                            status: '{{ $item['status'] ?? 'incomplete' }}'
                                                                        })" />
                                                                </x-slot>

                                                                <x-slot name="heading">
                                                                    <div class="flex items-center gap-2">
                                                                        <x-filament::icon
                                                                            icon="heroicon-o-pencil-square"
                                                                            class="w-6 h-6 text-pink-500" />
                                                                        <span class="font-semibold text-lg">Edit
                                                                            Checklist</span>
                                                                    </div>
                                                                </x-slot>

                                                                <div class="space-y-2">
                                                                    <label for="task-name"
                                                                        class="text-sm font-medium text-gray-700 dark:text-gray-300">Task
                                                                        Name</label>
                                                                    <x-filament::input.wrapper>
                                                                        <x-filament::input type="text"
                                                                            wire:model.defer="currentCheckItemName"
                                                                            value="{{ $item['check_item_name'] ?? '' }}" />
                                                                    </x-filament::input.wrapper>
                                                                    @error('currentCheckItemName')
                                                                        <div class="text-xs text-danger-500">
                                                                            {{ $message }}</div>
                                                                    @enderror
                                                                </div>

                                                                <div class="space-y-2">
                                                                    <label for="due-date"
                                                                        class="text-sm font-medium text-gray-700 dark:text-gray-300">Due
                                                                        Date</label>
                                                                    <x-filament::input.wrapper>
                                                                        <x-filament::input type="date"
                                                                            wire:model.defer="checkItemDueDate"
                                                                            value="{{ $item['due_date'] ?? '' }}" />
                                                                    </x-filament::input.wrapper>
                                                                    @error('checkItemDueDate')
                                                                        <div class="text-xs text-danger-500">
                                                                            {{ $message }}</div>
                                                                    @enderror
                                                                </div>

                                                                <div class="space-y-2">
                                                                    <label for="user"
                                                                        class="text-sm font-medium text-gray-700 dark:text-gray-300">Assign
                                                                        To</label>
                                                                    <x-filament::input.wrapper>
                                                                        <x-filament::input.select
                                                                            wire:model.defer="assignedUser"
                                                                            value="{{ $item['user_id'] ?? '' }}">
                                                                            <option value="">Select User</option>
                                                                            @foreach ($users as $user)
                                                                                <option value="{{ $user->id }}">
                                                                                    {{ $user->name }}</option>
                                                                            @endforeach
                                                                        </x-filament::input.select>
                                                                    </x-filament::input.wrapper>
                                                                    @error('assignedUser')
                                                                        <div class="text-xs text-danger-500">
                                                                            {{ $message }}</div>
                                                                    @enderror
                                                                </div>

                                                                <div class="space-y-2">
                                                                    <label for="status"
                                                                        class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                                                    <x-filament::input.wrapper>
                                                                        <x-filament::input.select
                                                                            wire:model.defer="checkItemStatus"
                                                                            value="{{ $item['status'] ?? 'incomplete' }}">
                                                                            <option value="incomplete">Incomplete
                                                                            </option>
                                                                            <option value="complete">Complete</option>
                                                                        </x-filament::input.select>
                                                                    </x-filament::input.wrapper>
                                                                    @error('checkItemStatus')
                                                                        <div class="text-xs text-danger-500">
                                                                            {{ $message }}</div>
                                                                    @enderror
                                                                </div>

                                                                <x-slot name="footerActions">
                                                                    <div class="flex justify-end items-center gap-2">
                                                                        <x-filament::button
                                                                            wire:click="updateTask('{{ $card['card_id'] }}', '{{ $checklist['checklist_id'] }}', '{{ $item['check_item_id'] }}')">
                                                                            <span wire:loading.remove>Edit Task</span>
                                                                            <span wire:loading
                                                                                class="flex items-center gap-1">
                                                                                Editing...
                                                                            </span>
                                                                        </x-filament::button>
                                                                    </div>
                                                                </x-slot>
                                                            </x-filament::modal>
                                                        </div>
                                                        <div class="flex gap-2 p-1">
                                                            <x-filament::modal>
                                                                <x-slot name="trigger">
                                                                    <x-filament::icon-button icon="heroicon-o-calendar"
                                                                        wire:click="openChecklistModal('{{ $card['card_id'] }}', '{{ $checklist['checklist_id'] }}')"
                                                                        tooltip="Edit Due date"
                                                                        x-on:click="$wire.setCurrentTask({
                                                                            check_item_id: '{{ $item['check_item_id'] ?? '' }}',
                                                                            due_date: '{{ $item['due_date'] ?? '' }}'
                                                                        })" />
                                                                </x-slot>

                                                                <x-slot name="heading">
                                                                    <div class="flex items-center gap-2">
                                                                        <x-filament::icon icon="heroicon-o-calendar"
                                                                            class="w-6 h-6 text-pink-500" />
                                                                        <span class="font-semibold text-lg">Edit Due
                                                                            Date</span>
                                                                    </div>
                                                                </x-slot>

                                                                <div class="space-y-2">
                                                                    <label for="due-date"
                                                                        class="text-sm font-medium text-gray-700 dark:text-gray-300">Due
                                                                        Date</label>
                                                                    <x-filament::input.wrapper>
                                                                        <x-filament::input type="date"
                                                                            wire:model.defer="checkItemDueDate"
                                                                            value="{{ $item['due_date'] ?? '' }}" />
                                                                    </x-filament::input.wrapper>
                                                                    @error('checkItemDueDate')
                                                                        <div class="text-xs text-danger-500">
                                                                            {{ $message }}</div>
                                                                    @enderror
                                                                </div>

                                                                <x-slot name="footerActions">
                                                                    <div class="flex justify-end items-center gap-2">
                                                                        <x-filament::button
                                                                            wire:click="updateTaskDueDate('{{ $card['card_id'] }}', '{{ $checklist['checklist_id'] }}', '{{ $item['check_item_id'] }}')">
                                                                            <span wire:loading.remove>Edit Task</span>
                                                                            <span wire:loading
                                                                                class="flex items-center gap-1">
                                                                                Editing...
                                                                            </span>
                                                                        </x-filament::button>
                                                                    </div>
                                                                </x-slot>
                                                            </x-filament::modal>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                {{ !empty($item['due_date']) ? \Carbon\Carbon::parse($item['due_date'])->format('F d, Y') : 'No Due Date' }}
                                                            </p>
                                                        </div>
                                                        <div class="flex gap-2 p-1">
                                                            <x-filament::modal>
                                                                <x-slot name="trigger">
                                                                    <x-filament::icon-button icon="heroicon-o-user"
                                                                        wire:click="openChecklistModal('{{ $card['card_id'] }}', '{{ $checklist['checklist_id'] }}')"
                                                                        tooltip="Assigned to: {{ optional(\App\Models\User::find($item['user_id']))->name ?? 'Unknown' }}"
                                                                        x-on:click="$wire.setCurrentTask({
                                                                            check_item_id: '{{ $item['check_item_id'] ?? '' }}',
                                                                            user_id: '{{ $item['user_id'] ?? '' }}'
                                                                        })" />
                                                                </x-slot>

                                                                <x-slot name="heading">
                                                                    <div class="flex items-center gap-2">
                                                                        <x-filament::icon icon="heroicon-o-user"
                                                                            class="w-6 h-6 text-pink-500" />
                                                                        <span class="font-semibold text-lg">Edit
                                                                            Assignee</span>
                                                                    </div>
                                                                </x-slot>

                                                                <div class="space-y-2">
                                                                    <label for="user"
                                                                        class="text-sm font-medium text-gray-700 dark:text-gray-300">Assign
                                                                        To</label>
                                                                    <x-filament::input.wrapper>
                                                                        <x-filament::input.select
                                                                            wire:model.defer="assignedUser"
                                                                            value="{{ $item['user_id'] ?? '' }}">
                                                                            <option value="">Select User</option>
                                                                            @foreach ($users as $user)
                                                                                <option value="{{ $user->id }}">
                                                                                    {{ $user->name }}</option>
                                                                            @endforeach
                                                                        </x-filament::input.select>
                                                                    </x-filament::input.wrapper>
                                                                    @error('assignedUser')
                                                                        <div class="text-xs text-danger-500">
                                                                            {{ $message }}</div>
                                                                    @enderror
                                                                </div>

                                                                <x-slot name="footerActions">
                                                                    <div class="flex justify-end items-center gap-2">
                                                                        <x-filament::button
                                                                            wire:click="updateTaskUser('{{ $card['card_id'] }}', '{{ $checklist['checklist_id'] }}', '{{ $item['check_item_id'] }}')">
                                                                            <span wire:loading.remove>Edit Task</span>
                                                                            <span wire:loading
                                                                                class="flex items-center gap-1">
                                                                                Editing...
                                                                            </span>
                                                                        </x-filament::button>
                                                                    </div>
                                                                </x-slot>
                                                            </x-filament::modal>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                @if (!empty($item['user_id']))
                                                                    {{ optional(\App\Models\User::find($item['user_id']))->name ?? 'Unknown' }}
                                                                @else
                                                                    No Assigned User
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="mt-2 flex gap-2">
                                                            @if (strtolower($item['status'] ?? '') === 'complete')
                                                                <x-filament::badge color="success"
                                                                    class="px-3 py-1 text-sm"
                                                                    icon="heroicon-o-check-circle">
                                                                    Complete
                                                                </x-filament::badge>
                                                            @elseif (strtolower($item['status'] ?? '') === 'pending')
                                                                <x-filament::badge color="info"
                                                                    class="px-3 py-1 text-sm" icon="heroicon-o-clock">
                                                                    Pending
                                                                </x-filament::badge>
                                                            @else
                                                                <x-filament::badge color="warning"
                                                                    class="px-3 py-1 text-sm"
                                                                    icon="heroicon-o-x-circle">
                                                                    Incomplete
                                                                </x-filament::badge>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center justify-end">
                                                        <x-filament::modal width="xs">
                                                            <x-slot name="trigger">
                                                                @if (strtolower($item['status'] ?? '') === 'incomplete')
                                                                    <x-filament::icon-button
                                                                        icon="heroicon-o-check-circle" color="primary"
                                                                        size="lg"
                                                                        wire:click="openStateModal('{{ $card['card_id'] }}', '{{ $checklist['checklist_id'] }}', '{{ $item['check_item_id'] }}')"
                                                                        tooltip="Submit Task as Complete" />
                                                                @else
                                                                    <x-filament::icon-button icon="heroicon-o-x-circle"
                                                                        color="danger" size="lg"
                                                                        wire:click="openStateModal('{{ $card['card_id'] }}', '{{ $checklist['checklist_id'] }}', '{{ $item['check_item_id'] }}')"
                                                                        tooltip="Mark Task as Incomplete" />
                                                                @endif
                                                            </x-slot>

                                                            <div
                                                                class="flex flex-col items-center justify-center py-6 space-y-4">
                                                                @if (strtolower($item['status'] ?? '') === 'complete')
                                                                    <x-filament::icon icon="heroicon-o-x-circle"
                                                                        class="w-16 h-16 text-danger-500"
                                                                        color="danger" />
                                                                    <p
                                                                        class="text-center text-gray-600 dark:text-gray-400">
                                                                        Are you sure you want to mark this task as
                                                                        incomplete?
                                                                    </p>
                                                                @else
                                                                    <x-filament::icon icon="heroicon-o-check-circle"
                                                                        class="w-16 h-16 text-success-500"
                                                                        color="primary" />
                                                                    <p
                                                                        class="text-center text-gray-600 dark:text-gray-400">
                                                                        Are you sure you want to do this action?
                                                                    </p>
                                                                @endif
                                                            </div>

                                                            <x-slot name="footer">
                                                                <div class="flex justify-end items-center gap-2">
                                                                    <x-filament::button color="gray"
                                                                        x-on:click="close">
                                                                        Cancel
                                                                    </x-filament::button>
                                                                    <x-filament::button wire:click="confirmAction"
                                                                        color="{{ strtolower($item['status'] ?? '') === 'pending' ? 'danger' : 'primary' }}">
                                                                        Confirm
                                                                    </x-filament::button>
                                                                </div>
                                                            </x-slot>
                                                        </x-filament::modal>
                                                    </div>
                                                </div>
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
