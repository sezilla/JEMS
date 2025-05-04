<div wire:init="loadProgress">
    @if($loading)
        <div class="grid grid-cols-3 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @for($i=0; $i<6; $i++)
                <x-filament::card class="animate-pulse h-32 bg-gray-800"></x-filament::card>
            @endfor
        </div>
    @else
    @if (auth()->user()->hasRole('Coordinator'))
        <div class="grid grid-cols-3 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($progress as $cardName => $percentage)
                <x-filament::card class="bg-gradient-to-b from-gray-800 to-gray-900 shadow-lg rounded-xl transition-transform transform hover:scale-105 duration-200">
                    <div class="grid grid-rows-3">
                        <h3 class="text-xl font-extrabold text-white text-center tracking-wide drop-shadow">
                            {{ $cardName }}
                        </h3>
                        <div class="w-5/6 mx-auto h-1 rounded-full bg-white opacity-70"></div>
                        <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                            <svg class="w-full h-full drop-shadow-lg" viewBox="0 0 36 36">
                                <!-- Background circle -->
                                <path
                                    d="M18 2.0845
                                        a 15.9155 15.9155 0 0 1 0 31.831
                                        a 15.9155 15.9155 0 0 1 0 -31.831"
                                    fill="none"
                                    stroke="#374151"
                                    stroke-width="3.5"
                                />
                                <!-- Progress circle -->
                                <path
                                    d="M18 2.0845
                                        a 15.9155 15.9155 0 0 1 0 31.831
                                        a 15.9155 15.9155 0 0 1 0 -31.831"
                                    fill="none"
                                    stroke="url(#progressGradient)"
                                    stroke-width="4"
                                    stroke-dasharray="{{ $percentage }}, 100"
                                    style="transition: stroke-dasharray 0.6s cubic-bezier(.4,2,.6,1);"
                                />
                                <defs>
                                    <linearGradient id="progressGradient" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#ec4899"/>
                                        <stop offset="100%" stop-color="#3b82f6"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <span class="absolute text-xl font-bold text-white drop-shadow">
                                {{ $percentage }}%
                            </span>
                        </div>
                    </div>
                </x-filament::card>
            @endforeach
        </div>
    @endif
    @endif
</div>