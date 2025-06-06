@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
@endphp

<x-filament-widgets::widget>
        @php
            $user = Auth::user();
            $department = $user?->departments?->first();
            $team = $user?->teams; // Fix: get the correct team model
        @endphp
        
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-900 rounded-xl p-6 border-l-4 border-primary-500">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-5">
                <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>
            
            <!-- Main Content -->
            <div class="relative z-10">
                <!-- Header Section -->
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6 mb-6">
                    <!-- Avatar Section -->
                    <div class="flex-shrink-0">
                        <div class="relative group">
                            @if($user && $user->getFilamentAvatarUrl())
                                <div class="relative">
                                    <img src="{{ $user->getFilamentAvatarUrl() }}" 
                                         alt="{{ $user->name }}'s Avatar" 
                                         class="w-20 h-20 lg:w-24 lg:h-24 rounded-full border-4 border-white dark:border-gray-700 shadow-xl object-cover transition-transform duration-300 group-hover:scale-105">
                                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white dark:border-gray-700 shadow-sm"></div>
                                </div>
                            @else
                                <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 border-4 border-white dark:border-gray-700 shadow-xl flex items-center justify-center transition-transform duration-300 group-hover:scale-105">
                                    <span class="text-white text-2xl font-bold">{{ substr($user?->name ?? 'U', 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- User Info Section -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="min-w-0">
                                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white truncate">
                                    {{ $user?->name ?? 'Unknown User' }}
                                </h2>
                                <p class="text-sm lg:text-base text-gray-600 dark:text-gray-300 flex items-center gap-2 mt-1">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    <span class="truncate">{{ $user?->email ?? 'No email provided' }}</span>
                                </p>
                            </div>
                            
                            <!-- Role Badge -->
                            <div class="flex-shrink-0">
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 rounded-full shadow-md border border-gray-200 dark:border-gray-700">
                                    <div class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></div>
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Role:</span>
                                    <span class="text-sm font-bold text-primary-600 dark:text-primary-400">
                                        {{ $user?->getRoleNames()?->implode(', ') ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Details Cards Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Department Card -->
                    <div class="group bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border-l-4 border-emerald-500 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                @if($department && isset($department->image))
                                    <div class="relative">
                                        <img src="{{ Storage::url($department->image) }}" 
                                             alt="{{ $department->name }} Logo" 
                                             class="w-12 h-12 rounded-lg object-cover border-2 border-emerald-200 dark:border-emerald-600 shadow-sm group-hover:scale-110 transition-transform duration-300">
                                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full border border-white dark:border-gray-800"></div>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5zm8 8v2a1 1 0 01-1 1H6a1 1 0 01-1-1v-2h8z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 uppercase tracking-wide">Department</span>
                                    <div class="w-1 h-1 bg-emerald-400 rounded-full"></div>
                                </div>
                                <p class="text-base font-bold text-gray-900 dark:text-white truncate">
                                    {{ $user?->departments?->pluck('name')->implode(', ') ?? 'Not Assigned' }}
                                </p>
                                @if($department && $department->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                        {{ $department->description }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Team Card -->
                    <div class="group bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border-l-4 border-blue-500 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                @if($team && isset($team->image))
                                    <div class="relative">
                                        <img src="{{ Storage::url($team->image) }}" 
                                             alt="{{ $team->name }} Logo" 
                                             class="w-12 h-12 rounded-lg object-cover border-2 border-blue-200 dark:border-blue-600 shadow-sm group-hover:scale-110 transition-transform duration-300">
                                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 rounded-full border border-white dark:border-gray-800"></div>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                            <path d="M6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.97 5.97 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-semibold text-blue-700 dark:text-blue-400 uppercase tracking-wide">Team</span>
                                    <div class="w-1 h-1 bg-blue-400 rounded-full"></div>
                                </div>
                                <p class="text-base font-bold text-gray-900 dark:text-white truncate">
                                    {{ $team?->name ?? 'Not Assigned' }}
                                </p>
                                @if($team && $team->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                        {{ $team->description }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Info Section -->
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                <span>Member since {{ $user?->created_at?->format('M Y') ?? 'N/A' }}</span>
                            </div>
                            @if($user?->email_verified_at)
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-green-600 dark:text-green-400">Email Verified</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-filament-widgets::widget>