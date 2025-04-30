{{-- resources/views/filament/widgets/overall-reports-widget.blade.php --}}
<x-filament::widget>
    <x-filament::card>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Overall Reports of Projects</h2>

            @php
                // Grab the entire `tableFilters` array from the URL (?tableFilters[...]=...)
                $filters = request()->query('tableFilters', []);

                // Within that, get the wedding_date_range sub-array (or an empty array)
                $dateRange = $filters['wedding_date_range'] ?? [];

                // Finally pull out each individual value (or null)
                $weddingDateFrom  = $dateRange['wedding_date_from'] ?? null;
                $weddingDateUntil = $dateRange['wedding_date_until'] ?? null;
            @endphp

            <a
                href="{{ route('projects.report.download', [
                    'wedding_date_from' => $weddingDateFrom,
                    'wedding_date_until' => $weddingDateUntil,
                ]) }}"
                target="_blank"
                class="filament-button filament-button--primary"
            >
                Download PDF Report
            </a>
        </div>

        {{ $this->table }}
    </x-filament::card>
</x-filament::widget>
