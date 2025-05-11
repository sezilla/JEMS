<div>
    @if($loading)
        <div class="flex flex-col items-center justify-center min-h-[400px] space-y-4">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500"></div>
            <p class="text-gray-600 dark:text-gray-400">Loading tasks...</p>
        </div>
    @else
        <div>
            <h1 class="text-2xl font-bold mb-4">Project Task</h1>
            @if($card && !empty($card->user_checklist))
                <x-filament::section class="mb-8">
                    <header class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $card->card_name ?? 'Department Card' }}
                        </h2>
                    </header>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($card->user_checklist as $checklist)
                            <x-filament::card>
                                <header class="mb-2 flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        {{ $checklist['checklist_name'] ?? 'Checklist' }}
                                    </h3>
                                </header>
                                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($checklist['checklist_items'] ?? [] as $item)
                                        <li class="py-3 flex items-center justify-between">
                                            <div>
                                                <div class="flex gap-4">
                                                    <p class="font-semibold text-gray-800 dark:text-gray-200">
                                                        {{ $item['checklist_item_name'] ?? 'Task' }}
                                                    </p>
                                                </div>
                                                <div class="flex gap-2 p-1">
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ !empty($item['checklist_item_due']) ? \Carbon\Carbon::parse($item['checklist_item_due'])->format('F d, Y') : 'No Due Date' }}
                                                    </p>
                                                </div>
                                                <div class="flex gap-2 p-1">
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        @if (!empty($item['checklist_item_user']))
                                                            {{ optional(\App\Models\User::find($item['checklist_item_user']))->name ?? 'Unknown' }}
                                                        @else
                                                            No Assigned User
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="mt-2 flex gap-2">
                                                    @if (strtolower($item['checklist_item_status'] ?? '') === 'complete')
                                                        <x-filament::badge color="success" class="px-3 py-1 text-sm" icon="heroicon-o-check-circle">
                                                            Complete
                                                        </x-filament::badge>
                                                    @else
                                                        <x-filament::badge color="warning" class="px-3 py-1 text-sm" icon="heroicon-o-x-circle">
                                                            Incomplete
                                                        </x-filament::badge>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </x-filament::card>
                        @endforeach
                    </div>
                </x-filament::section>
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
