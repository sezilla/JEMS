@php
    $projectId = $projectId ?? null;
    $allowedRoles = ['super admin', 'Hr Admin', 'Department Admin'];
    $hasAllowedRole = auth()->check() && auth()->user()->hasAnyRole($allowedRoles);
@endphp

<div>
    {{-- Main Progress Loader --}}
    <div
        x-data="{
            visible: @entangle('isVisible').live,
            progress: @entangle('progress').live,
            status: @entangle('status').live,
            message: @entangle('message').live,
            hasError: @entangle('hasError').live,
            isCompleted: @entangle('isCompleted').live,
            autoHideTimer: null,
            
            init() {
                console.log('Initializing progress loader for project:', '{{ $projectId }}');
                
                // Watch for completion to auto-hide
                this.$watch('isCompleted', (value) => {
                    if (value && !this.hasError) {
                        this.scheduleAutoHide();
                    }
                });
                
                // Listen for auto-hide event from Livewire
                Livewire.on('auto-hide-progress', () => {
                    this.scheduleAutoHide();
                });
            },
            
            scheduleAutoHide() {
                if (this.autoHideTimer) {
                    clearTimeout(this.autoHideTimer);
                }
                this.autoHideTimer = setTimeout(() => {
                    this.visible = false;
                }, 3000);
            },
            
            getProgressBarClass() {
                if (this.hasError) return 'bg-red-500';
                if (this.isCompleted) return 'bg-green-500';
                return 'bg-blue-500';
            },
            
            getStatusColor() {
                if (this.hasError) return 'text-red-600';
                if (this.isCompleted) return 'text-green-600';
                return 'text-blue-600';
            }
        }"
        x-show="visible"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed top-4 right-4 w-80 bg-white rounded-lg shadow-lg border p-4 z-50"
        wire:key="progress-loader-{{ $projectId }}"
    >
        <div class="space-y-4">
            {{-- Top Row: Status (left) and Percentage (right) --}}
            <div class="flex justify-between items-center">
                {{-- Status with Icon (Top Left) --}}
                <div class="flex items-center gap-2">
                    {{-- Status Icon --}}
                    <div x-show="hasError">
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div x-show="isCompleted && !hasError">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div x-show="!hasError && !isCompleted">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
                    </div>
                    
                    {{-- Status Text --}}
                    <span class="text-sm font-medium" 
                        :class="getStatusColor()"
                        x-text="status">
                    </span>
                </div>
                
                {{-- Percentage and Close Button (Top Right) --}}
                <div class="flex items-center gap-3">
                    {{-- Progress Percentage --}}
                    <div class="text-sm font-semibold">
                        <span x-show="hasError" class="text-red-600">Error</span>
                        <span x-show="isCompleted && !hasError" class="text-green-600">100%</span>
                        <span x-show="progress !== null && progress < 100 && !hasError" 
                              :class="getStatusColor()"
                              x-text="progress + '%'"></span>
                        <span x-show="progress === null && !hasError && !isCompleted" 
                              class="text-blue-600">...</span>
                    </div>
                    
                    {{-- Close Button --}}
                    <button 
                        @click="visible = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded hover:bg-gray-100"
                        type="button"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            {{-- Progress Bar (Middle) --}}
            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                <div 
                    class="h-2.5 transition-all duration-500 ease-out rounded-full"
                    :class="getProgressBarClass()"
                    :style="'width: ' + (progress !== null ? progress : 0) + '%'"
                ></div>
            </div>
            
            {{-- Message (Bottom) --}}
            <div x-show="message" 
                 class="text-sm text-gray-600 leading-relaxed" 
                 x-text="message"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
            </div>
        </div>
    </div>

    {{-- Debug Information (remove in production) --}}
    @if(config('app.debug'))
    <div class="fixed bottom-4 right-4 bg-black text-white text-xs p-2 rounded max-w-xs">
        <div>Project ID: {{ $projectId }}</div>
        <div>Progress: {{ $progress }}</div>
        <div>Status: {{ $status }}</div>
        <div>Visible: {{ $isVisible ? 'true' : 'false' }}</div>
        <div>Completed: {{ $isCompleted ? 'true' : 'false' }}</div>
        <div>Error: {{ $hasError ? 'true' : 'false' }}</div>
    </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            console.log('ProgressLoader DOM loaded');
            
            // Debug Echo connection
            if (typeof window.Echo !== 'undefined') {
                console.log('Echo is available');
                
                // Log connection status
                if (window.Echo.connector && window.Echo.connector.pusher) {
                    window.Echo.connector.pusher.connection.bind('connected', () => {
                        console.log('Echo connected to Reverb');
                    });
                    
                    window.Echo.connector.pusher.connection.bind('disconnected', () => {
                        console.log('Echo disconnected from Reverb');
                    });
                    
                    window.Echo.connector.pusher.connection.bind('error', (error) => {
                        console.error('Echo connection error:', error);
                    });
                }
            } else {
                console.warn('Echo is not available - check if Laravel Echo is properly configured');
            }
        });
    </script>
    @endpush
</div>