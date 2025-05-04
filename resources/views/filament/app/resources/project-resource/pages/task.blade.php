<x-filament::page>
    <!-- Project Details Section -->
    <x-filament::section>
        <!-- Main Project Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <!-- Project Image -->
            <div class="space-y-2">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Thumbnail Image</h3>
                <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden shadow-sm">
                    @if($project->thumbnail_path)
                        <img src="{{ Storage::url($project->thumbnail_path) }}" 
                             alt="{{ $project->name }}" 
                             class="object-cover w-full h-full">
                    @else
                        <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <x-filament::icon icon="heroicon-o-photo" class="w-8 h-8 text-gray-400" />
                        </div>
                    @endif
                </div>
            </div>

            <!-- Project Basic Info -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Event Name</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $project->name }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Package</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $project->package->name }}
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
                    <p class="text-lg font-semibold">
                        @php
                            $statusConfig = config('project.project_status');
                            $statusKey = array_search($project->status, $statusConfig);
                            
                            $statusColor = match($statusKey) {
                                'active' => 'success',
                                'completed' => 'success',
                                'archived' => 'gray',
                                'canceled' => 'danger',
                                'on_hold' => 'warning',
                                default => 'gray'
                            };
                            
                            $statusIcon = match($statusKey) {
                                'active' => 'heroicon-o-play-circle',
                                'completed' => 'heroicon-o-check-circle',
                                'archived' => 'heroicon-o-archive-box',
                                'canceled' => 'heroicon-o-x-circle',
                                'on_hold' => 'heroicon-o-pause-circle',
                                default => 'heroicon-o-question-mark-circle'
                            };
                            
                            $statusText = match($statusKey) {
                                'active' => 'Active',
                                'completed' => 'Completed',
                                'archived' => 'Archived',
                                'canceled' => 'Canceled',
                                'on_hold' => 'On Hold',
                                default => 'Unknown'
                            };
                        @endphp
                        <x-filament::badge color="{{ $statusColor }}" class="inline-flex flex-row items-center gap-2">
                            <x-filament::icon :icon="$statusIcon" class="w-4 h-4" />
                            <span>{{ $statusText }}</span>
                        </x-filament::badge>
                    </p>
                </div>
            </div>

            <!-- Project Dates -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Event Date</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ \Carbon\Carbon::parse($project->end_date)->format('F d, Y') }}
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $project->venue ?? 'Not specified' }}
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Teams</h3>
                    <div class="space-y-1">
                        @forelse($project->teams as $team)
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $team->name }}
                            </p>
                        @empty
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                No teams assigned
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards Section -->
        <div class="flex flex-col gap-4">
            <!-- Project Description -->
            <x-filament::card>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $project->description }}</p>
            </x-filament::card>

            <!-- Special Request -->
            @if($project->special_request)
                <x-filament::card>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Special Request</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ $project->special_request }}</p>
                </x-filament::card>
            @endif

            <!-- Coordinators -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-filament::card>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Head Coordinator</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $project->headCoordinator->name ?? 'Not assigned' }}
                            </p>
                        </div>
                        @if($project->head_coor_assistant)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assistant</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $project->head_coor_assistant->name }}
                                </p>
                            </div>
                        @endif
                    </div>
                </x-filament::card>
                <x-filament::card>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Bride Coordinator</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $project->brideCoordinator->name ?? 'Not assigned' }}
                            </p>
                        </div>
                        @if($project->bride_coor_assistant)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assistant</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $project->bride_coor_assistant->name }}
                                </p>
                            </div>
                        @endif
                    </div>
                </x-filament::card>
                <x-filament::card>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Groom Coordinator</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $project->groomCoordinator->name ?? 'Not assigned' }}
                            </p>
                        </div>
                        @if($project->groom_coor_assistant)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assistant</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $project->groom_coor_assistant->name }}
                                </p>
                            </div>
                        @endif
                    </div>
                </x-filament::card>
            </div>
        </div>
    </x-filament::section>

    <!-- Project Progress Widget -->
    {{-- <x-filament::section>
        <x-filament-widgets::widget class="\App\Filament\App\Resources\ProjectResource\Widgets\ProjectProgress">
            <x-slot name="record">
                {{ $project }}
            </x-slot>
        </x-filament-widgets::widget>
    </x-filament::section> --}}

    <!-- Project Progress Section -->
    <x-filament::section>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($progress as $cardName => $percentage)
                <x-filament::card>
                    <div class="flex flex-col items-center space-y-4 p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center">
                            {{ $cardName }}
                        </h3>
                        
                        <div class="relative w-32 h-32">
                            <svg class="w-full h-full" viewBox="0 0 36 36">
                                <!-- Background circle -->
                                <path
                                    d="M18 2.0845
                                        a 15.9155 15.9155 0 0 1 0 31.831
                                        a 15.9155 15.9155 0 0 1 0 -31.831"
                                    fill="none"
                                    stroke="#e5e7eb"
                                    stroke-width="3"
                                    class="dark:stroke-gray-700"
                                />
                                <!-- Progress circle -->
                                <path
                                    d="M18 2.0845
                                        a 15.9155 15.9155 0 0 1 0 31.831
                                        a 15.9155 15.9155 0 0 1 0 -31.831"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="3"
                                    stroke-dasharray="{{ $percentage }}, 100"
                                    class="text-primary-500"
                                />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $percentage }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </x-filament::card>
            @empty
                <div class="col-span-full">
                    <p class="text-gray-500 dark:text-gray-400 text-center">
                        No progress data available
                    </p>
                </div>
            @endforelse
        </div>
    </x-filament::section>

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
            <div class="p-6 text-gray-600 dark:text-gray-400">No tasks found.</div>
        @endif
    </div>
</x-filament::page>
