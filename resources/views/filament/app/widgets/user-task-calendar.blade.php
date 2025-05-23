<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-2">
            <h2 class="text-xl font-bold">
                Welcome, {{ $user->name ?? 'User' }}!
            </h2>
            <div>
                <strong>Email:</strong> {{ $user->email }}
            </div>
            @if($user->getFilamentAvatarUrl())
                <img src="{{ $user->getFilamentAvatarUrl() }}" alt="Avatar" class="w-16 h-16 rounded-full mt-2">
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
