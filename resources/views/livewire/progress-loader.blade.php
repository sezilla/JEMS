@php
    $projectId = $projectId ?? null;
    $allowedRoles = ['super admin', 'Hr Admin', 'Department Admin'];
    $hasAllowedRole = auth()->check() && auth()->user()->hasAnyRole($allowedRoles);
@endphp

<div
    x-data="{
        visible: @entangle('isVisible'),
        progress: @entangle('progress'),
        status: @entangle('status'),
        message: @entangle('message'),
        hasError: @entangle('hasError'),
        isCompleted: @entangle('isCompleted'),
        autoHideTimer: null,
        
        init() {
            console.log('Initializing progress loader for project:', '{{ $projectId }}');
            
            // Listen for Livewire events
            this.$wire.on('progress-updated', (data) => {
                console.log('Received progress update:', data);
                this.updateProgress(data[0] || data);
            });

            // Listen for auto-hide event
            this.$wire.on('auto-hide-progress', () => {
                if (this.autoHideTimer) clearTimeout(this.autoHideTimer);
                this.autoHideTimer = setTimeout(() => {
                    this.visible = false;
                }, 3000);
            });

            // Echo listener for real-time updates
            @if($projectId)
            if (typeof window.Echo !== 'undefined') {
                console.log('Setting up Echo listener for channel: project.progress.{{ $projectId }}');
                
                window.Echo.channel('project.progress.{{ $projectId }}')
                    .listen('ProgressUpdated', (e) => {
                        console.log('Received Echo ProgressUpdated event:', e);
                        this.updateProgress(e);
                        // Also trigger Livewire refresh
                        this.$wire.$refresh();
                    });
            } else {
                console.warn('Echo is not available');
            }
            @endif
        },
        
        updateProgress(data) {
            console.log('Updating progress with data:', data);
            
            if (typeof data === 'object' && data !== null) {
                this.progress = data.progress ?? this.progress;
                this.status = data.status || 'Processing';
                this.message = data.message || '';
                this.hasError = data.has_error ?? false;
                this.isCompleted = data.is_completed ?? false;
                
                // Show progress bar if there's actual progress
                if (data.progress > 0) {
                    this.visible = true;
                }

                // Handle completion
                if (data.progress >= 100 && !this.hasError) {
                    this.isCompleted = true;
                    if (this.autoHideTimer) clearTimeout(this.autoHideTimer);
                    this.autoHideTimer = setTimeout(() => {
                        this.visible = false;
                    }, 3000);
                }
            }
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
    wire:key="progress-loader-{{ $projectId }}"
>
    <div class="">
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
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-pink-500"></div>
                </div>
                
                {{-- Status Text --}}
                <span class="text-sm font-medium" 
                    :class="{
                        'text-red-600': hasError,
                        'text-green-600': isCompleted && !hasError,
                        'text-pink-600': !hasError && !isCompleted
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
        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden"
             x-data="{ 
                progress: @entangle('progress')
             }">
            <div class="bg-blue-500 h-2.5 transition-all duration-300 ease-out"
                 :style="'width: ' + (progress || 0) + '%'"></div>
        </div>
        
        {{-- Message --}}
        <div x-show="message" 
             class="text-xs text-gray-600 mt-1" 
             x-text="message"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"></div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('ProgressLoader DOM loaded');
        
        // Debug Echo connection
        if (typeof window.Echo !== 'undefined') {
            console.log('Echo is available');
            
            // Log connection status
            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('Echo connected to Reverb');
            });
            
            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                console.log('Echo disconnected from Reverb');
            });
            
            window.Echo.connector.pusher.connection.bind('error', (error) => {
                console.error('Echo connection error:', error);
            });
        } else {
            console.warn('Echo is not available - check if Laravel Echo is properly configured');
        }
    });
</script>
@endpush