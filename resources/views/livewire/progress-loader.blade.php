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
        refreshInterval: null,
        
        init() {
            console.log('Initializing progress loader for project:', '{{ $projectId }}');
            
            // Start auto-refresh every 5 seconds
            this.startAutoRefresh();
            
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

            // Cleanup on destroy
            this.$watch('visible', (value) => {
                if (!value) {
                    this.stopAutoRefresh();
                }
            });
        },
        
        startAutoRefresh() {
            // Clear existing interval
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
            }
            
            // Start new interval - refresh every 5 seconds
            this.refreshInterval = setInterval(() => {
                if (this.visible && !this.isCompleted && !this.hasError) {
                    console.log('Auto-refreshing progress data...');
                    this.$wire.$refresh();
                }
            }, 5000);
            
            console.log('Auto-refresh started (every 5 seconds)');
        },
        
        stopAutoRefresh() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
                this.refreshInterval = null;
                console.log('Auto-refresh stopped');
            }
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
                    this.stopAutoRefresh(); // Stop refreshing when completed
                    if (this.autoHideTimer) clearTimeout(this.autoHideTimer);
                    this.autoHideTimer = setTimeout(() => {
                        this.visible = false;
                    }, 3000);
                }
                
                // Stop refreshing on error
                if (this.hasError) {
                    this.stopAutoRefresh();
                }
            }
        },
        
        // Cleanup function
        destroy() {
            this.stopAutoRefresh();
            if (this.autoHideTimer) {
                clearTimeout(this.autoHideTimer);
            }
        }
    }"
    x-show="visible && status !== 'idle'"
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
        
        {{-- Progress Bar with Animation --}}
        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden relative">
            {{-- Main Progress Bar --}}
            <div class="h-2.5 transition-all duration-500 ease-out rounded-full"
                 :class="{
                     'bg-blue-500': !hasError && !isCompleted,
                     'bg-green-500': isCompleted && !hasError,
                     'bg-red-500': hasError
                 }"
                 :style="'width: ' + (progress || 0) + '%'"></div>
            
            {{-- Animated Shimmer Effect for Active Progress --}}
            <div x-show="progress !== null && progress < 100 && !hasError && !isCompleted"
                 class="absolute top-0 left-0 h-full w-full">
                <div class="h-full bg-gradient-to-r from-transparent via-white to-transparent opacity-30 animate-pulse"
                     :style="'width: ' + (progress || 0) + '%'"></div>
            </div>
            
            {{-- Sliding Animation for Active Progress --}}
            <div x-show="progress !== null && progress < 100 && !hasError && !isCompleted"
                 class="absolute top-0 left-0 h-full overflow-hidden"
                 :style="'width: ' + (progress || 0) + '%'">
                <div class="h-full w-8 bg-gradient-to-r from-transparent via-blue-300 to-transparent animate-slide-right"></div>
            </div>
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

@push('styles')
<style>
    @keyframes slide-right {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(400%);
        }
    }
    
    .animate-slide-right {
        animation: slide-right 2s ease-in-out infinite;
    }
    
    /* Pulse animation for the shimmer effect */
    @keyframes pulse-glow {
        0%, 100% {
            opacity: 0.3;
        }
        50% {
            opacity: 0.7;
        }
    }
    
    .animate-pulse {
        animation: pulse-glow 1.5s ease-in-out infinite;
    }
</style>
@endpush

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
    
    // Cleanup intervals when page unloads
    window.addEventListener('beforeunload', () => {
        // Alpine.js will handle cleanup through the destroy method
    });
</script>
@endpush