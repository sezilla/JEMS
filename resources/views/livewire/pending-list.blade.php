<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    @if (auth()->user()->hasRole('Coordinator'))
        <h1 class="text-2xl font-bold mb-4">Pending Approval</h1>
        {{ $this->table }}
    @endif
</div>
