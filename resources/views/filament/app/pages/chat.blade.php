<x-filament-panels::page>
    <div 
        x-data="{ showContacts: true, showInfo: false }"
        class="flex space-x-4 min-h-[400px] md:min-h-[600px] lg:min-h-[700px] max-h-screen gap-2"
    >
        <!-- Toggleable Contacts Layout -->
        <div x-show="showContacts" class="flex-[2] max-h-full overflow-y-auto">
            @include('filament.app.pages.chatLayout.contacts', ['directContacts' => $directContacts, 'groupChats' => $groupChats])
        </div>

        <!-- Messages Section with Toggle Buttons -->
        <x-filament::section class="flex-1 max-h-full overflow-y-auto">
            <x-slot name="heading">
                <div class="flex justify-between items-center space-x-4">
                    <div class="flex space-x-2">
                        <!-- Toggle Contacts Button -->
                        <button @click="showContacts = !showContacts" class="focus:outline-none">
                            <x-filament::icon
                                icon="heroicon-o-user-group"
                                class="h-8 w-8 "
                            />
                        </button>
                        <h1 class="text-2xl font-bold px-2">Messages</h1>
                    </div>
                    <!-- Toggle Info Button -->
                    <button @click="showInfo = !showInfo" class="focus:outline-none">
                        <x-filament::icon
                            icon="heroicon-o-information-circle"
                            class="h-8 w-8"
                        />
                    </button>
                </div>
            </x-slot>

            @include('filament.app.pages.chatLayout.messageCard')
        </x-filament::section>

        <!-- Toggleable Info Layout -->
        <div x-show="showInfo" class="flex-[2] max-h-full overflow-y-auto">
            @include('filament.app.pages.chatLayout.info')
        </div>
    </div>
</x-filament-panels::page>
