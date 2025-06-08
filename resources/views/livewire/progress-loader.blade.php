@php
    $projectId = $projectId ?? null;
@endphp

<div
    x-data="{
        visible: {{ $projectId ? 'true' : 'false' }},
        progress: 0,
        status: 'idle',
        message: '',
        hasError: false,
        isCompleted: false,
        autoHideTimer: null,
        init() {
            console.log('Initializing progress loader for project:', '{{ $projectId }}');
            
            // Listen for Echo events
            Echo.channel('project.progress.{{ $projectId }}')
                .listen('ProgressUpdated', (e) => {
                    console.log('Received progress update:', e);
                    
                    this.progress = e.progress;
                    this.status = e.status || 'Processing';
                    this.message = e.message || '';
                    this.visible = true;
                    this.hasError = false;
                    this.isCompleted = false;

                    if (e.progress >= 100) {
                        this.isCompleted = true;
                        if (this.autoHideTimer) clearTimeout(this.autoHideTimer);
                        this.autoHideTimer = setTimeout(() => {
                            this.visible = false;
                        }, 3000);
                    }
                });
        }
    }"
    x-show="visible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="w-1/3 z-50"
>
    <div class="space-y-3">
        {{-- Header with status and percentage --}}
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                {{-- Status Icon --}}
                <div x-show="hasError">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div x-show="isCompleted && !hasError">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div x-show="!hasError && !isCompleted && progress === null">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
                </div>
                
                {{-- Status Text --}}
                <span class="text-sm font-medium" 
                    :class="{
                        'text-red-600': hasError,
                        'text-green-600': isCompleted && !hasError,
                        'text-blue-600': !hasError && !isCompleted
                    }"
                    x-text="hasError ? 'Error' : (isCompleted ? 'Completed' : status)">
                </span>
            </div>
            
            {{-- Progress Percentage --}}
            <div class="text-sm font-medium text-gray-700">
                <span x-show="hasError" class="text-red-600">!</span>
                <span x-show="isCompleted && !hasError" class="text-green-600">✓</span>
                <span x-show="progress !== null && progress < 100 && !hasError" x-text="progress + '%'"></span>
                <span x-show="progress === null && !hasError && !isCompleted">...</span>
            </div>
        </div>
        
        {{-- Progress Bar --}}
        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
            {{-- Error state --}}
            <div x-show="hasError" class="bg-red-500 h-2 w-full transition-all duration-500"></div>
            
            {{-- Completed state --}}
            <div x-show="isCompleted && !hasError" class="bg-green-500 h-2 w-full transition-all duration-500"></div>
            
            {{-- Indeterminate state --}}
            <div x-show="progress === null && !hasError && !isCompleted" 
                 class="bg-blue-500 h-2 animate-pulse" style="width: 40%;"></div>
            
            {{-- Progress state --}}
            <div x-show="progress !== null && progress >= 0 && progress < 100 && !hasError"
                 class="bg-blue-500 h-2 transition-all duration-700 ease-out"
                 :style="'width: ' + Math.max(5, progress) + '%'"></div>
        </div>
        
        {{-- Message --}}
        <div x-show="message" class="text-xs text-gray-600" x-text="message"></div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('ProgressLoader DOM loaded');
    });
</script>
@endpush