<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2 gap-4">
            <x-filament::section class="mb-4">
                <div class="h-64 flex items-center gap-4 space-x-4">
                    <x-filament::avatar 
                        :src="$avatar_url" 
                        alt="User Avatar" 
                        size="w-40 h-40"
                        class="m-4"
                    />

                    <div>
                        <h2 class="text-2xl font-bold">{{ $name }}</h2>
                        <p class="text-gray-600">{{ $role }}</p>
                        <p class="text-gray-500">{{ $department }}</p>
                        <p class="text-gray-500">{{ $team }}</p>
                    </div>
                </div>
            </x-filament::section>
            <x-filament::section class="mb-4">
                i dont know yet siguro bio??
            </x-filament::section>
            <x-filament::section class="mb-4">
                POSTS?????
            </x-filament::section>
        </div>
        <div class="md:col-start-3">
            <x-filament::section class="mb-4">
                i guess team, department idk
            </x-filament::section>
            <x-filament::section class="mb-4">
                ongoing projects???
            </x-filament::section>
            <x-filament::section class="mb-4">
                reset password
            </x-filament::section>
            <x-filament::section class="mb-4">
                browser sessions
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
