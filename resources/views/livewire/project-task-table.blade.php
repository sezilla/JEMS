<div class="flex flex-col gap-4">
    @if (!auth()->user()->hasRole('Coordinator'))
        <h1 class="text-2xl font-bold">Team's Tasks</h1>
    @endif
    @if (auth()->user()->hasRole('Coordinator'))
        <h1 class="text-2xl font-bold">Tasks</h1>
    @endif

    {{-- <x-filament::tabs label="Content tabs">
        <x-filament::tabs.item wire:click="setActiveTab('incomplete')" :active="$taskStatusTab === 'incomplete'"
            icon="heroicon-o-exclamation-circle" icon-position="after">
            Incomplete
        </x-filament::tabs.item>

        <x-filament::tabs.item wire:click="setActiveTab('complete')" :active="$taskStatusTab === 'completed'" icon="heroicon-o-check-circle"
            icon-position="after">
            Completed
        </x-filament::tabs.item>

        <x-filament::tabs.item wire:click="setActiveTab('pending')" :active="$taskStatusTab === 'pending'" icon="heroicon-o-clock"
            icon-position="after">
            Pending
        </x-filament::tabs.item>

        <x-filament::tabs.item wire:click="setActiveTab('all')" :active="$taskStatusTab === 'all'">
            All
        </x-filament::tabs.item>
    </x-filament::tabs> --}}

    {{ $this->table }}
</div>
