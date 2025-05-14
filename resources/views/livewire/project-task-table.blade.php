<div>
    @if (!auth()->user()->hasRole('Coordinator'))
        <h1 class="text-2xl font-bold mb-4">Team's Tasks</h1>
    @endif
    @if (auth()->user()->hasRole('Coordinator'))
        <h1 class="text-2xl font-bold mb-4">Tasks</h1>
    @endif

    {{ $this->table }}
</div>
