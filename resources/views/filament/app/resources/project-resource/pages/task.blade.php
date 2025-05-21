<x-filament::page>
    <!-- Project Details Section -->
    <x-filament::section>
        <!-- Main Project Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <!-- Project Image -->
            <div class="space-y-2">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Thumbnail Image</h3>
                <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden shadow-sm">
                    @if ($project->thumbnail_path)
                        <img src="{{ Storage::url($project->thumbnail_path) }}" alt="{{ $project->name }}"
                            class="object-cover w-full h-full"
                            style="width: 400px; height: 225px; max-width: 100%; max-height: 100%;" />
                    @else
                        <img src="https://placehold.co/400x400/gray/transparent?text=Event+Image" alt="Event Image"
                            class="object-cover w-full h-full"
                            style="width: 400px; height: 225px; max-width: 100%; max-height: 100%;" />
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
                            $status = $project->status;

                            $statusColor = $status->color() ?? 'gray';
                            $statusIcon = match ($status) {
                                App\Enums\ProjectStatus::ACTIVE => 'heroicon-o-play-circle',
                                App\Enums\ProjectStatus::COMPLETED => 'heroicon-o-check-circle',
                                App\Enums\ProjectStatus::ARCHIVED => 'heroicon-o-archive-box',
                                App\Enums\ProjectStatus::CANCELLED => 'heroicon-o-x-circle',
                                App\Enums\ProjectStatus::ON_HOLD => 'heroicon-o-pause-circle',
                                default => 'heroicon-o-question-mark-circle',
                            };

                            $statusText = $status->label() ?? 'Unknown';
                        @endphp
                    <div class="inline-flex">
                        <x-filament::badge color="{{ $statusColor }}">
                            <div class="flex gap-2">
                                <x-filament::icon :icon="$statusIcon" class="w-4 h-4" />
                                <span>{{ $statusText }}</span>
                            </div>
                        </x-filament::badge>
                    </div>
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Color</h3>
                    <div class="inline-flex items-center gap-2">
                        <div class="w-6 h-6 rounded" style="background-color: {{ $project->theme_color }};"></div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $project->theme_color }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Project Dates -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Event Date</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ \Carbon\Carbon::parse($project->end)->format('F d, Y') }}
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
                            <p class="text-md text-gray-900 dark:text-gray-100">
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
            @if ($project->description)
                <x-filament::card>
                    <div class="space-y-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</h3>
                        <div class="relative">
                            <div
                                class="text-gray-700 dark:text-gray-300 transition-all duration-300 ease-in-out {{ !$showFullDescription ? 'line-clamp-3' : '' }}">
                                {{ $project->description }}
                            </div>
                            @if (str_word_count($project->description) > 30)
                                <div class="mt-2">
                                    <x-filament::button wire:click="toggleDescription" wire:loading.attr="disabled"
                                        color="gray" size="sm" class="text-sm transition-opacity duration-200">
                                        <span wire:loading.remove wire:target="toggleDescription">
                                            {{ $showFullDescription ? 'Show Less' : 'See More...' }}
                                        </span>
                                        {{-- <span wire:loading wire:target="toggleDescription" class="inline-flex items-center">
                                            <x-filament::icon icon="heroicon-o-arrow-path" class="w-4 h-4 animate-spin mr-1" />
                                            Loading...
                                        </span> --}}
                                    </x-filament::button>
                                </div>
                            @endif
                        </div>
                    </div>
                </x-filament::card>
            @endif

            <!-- Special Request -->
            @if ($project->special_request)
                <x-filament::card>
                    <div class="space-y-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Special Request</h3>
                        <div class="relative">
                            <div
                                class="text-gray-700 dark:text-gray-300 transition-all duration-300 ease-in-out {{ !$showFullSpecialRequest ? 'line-clamp-3' : '' }}">
                                {{ $project->special_request }}
                            </div>
                            @if (str_word_count($project->special_request) > 30)
                                <div class="mt-2">
                                    <x-filament::button wire:click="toggleSpecialRequest" wire:loading.attr="disabled"
                                        color="gray" size="sm" class="text-sm transition-opacity duration-200">
                                        <span wire:loading.remove wire:target="toggleSpecialRequest">
                                            {{ $showFullSpecialRequest ? 'Show Less' : 'See More...' }}
                                        </span>
                                        {{-- <span wire:loading wire:target="toggleSpecialRequest" class="inline-flex items-center">
                                            <x-filament::icon icon="heroicon-o-arrow-path" class="w-4 h-4 animate-spin mr-1" />
                                            Loading...
                                        </span> --}}
                                    </x-filament::button>
                                </div>
                            @endif
                        </div>
                    </div>
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
                        @if ($project->head_coor_assistant)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assistant</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $project->headAssistant?->name ?? 'Not assigned' }}
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
                        @if ($project->bride_coor_assistant)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assistant</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $project->brideAssistant?->name ?? 'Not assigned' }}
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
                        @if ($project->groom_coor_assistant)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assistant</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $project->groomAssistant?->name ?? 'Not assigned' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </x-filament::card>
            </div>
        </div>
    </x-filament::section>

    <livewire:pending-list :project="$project" />

    <livewire:project-progress-cards :project="$project" />

    {{-- <livewire:project-trello-tasks :project="$project" /> --}}
    {{-- <livewire:project-task-table :project="$project" /> --}}
    {{-- <livewire:project-task :project="$project" /> --}}
</x-filament::page>
