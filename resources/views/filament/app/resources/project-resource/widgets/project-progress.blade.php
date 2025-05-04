<x-filament-widgets::widget>
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
</x-filament-widgets::widget>
