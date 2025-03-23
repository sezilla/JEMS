<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Profile Section -->
        <div class="md:col-span-2 space-y-6">
            <x-filament::section class="p-6 bg-white rounded-lg shadow">
                <div class="flex items-center gap-6">
                    <x-filament::avatar :src="$avatar_url" alt="User Avatar" size="w-32 h-32" class="rounded-full shadow-lg" />
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">{{ $name }}</h2>
                        <p class="text-lg text-gray-600">{{ $role }}</p>
                        <div class="mt-2 flex gap-2"+  >
                            <x-filament::badge class="px-3 py-1 text-sm">{{ $department }}</x-filament::badge>
                            <x-filament::badge class="px-3 py-1 text-sm">{{ $team }}</x-filament::badge>
                        </div>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="p-6 bg-white rounded-lg shadow">
                <h2 class="text-xl font-semibold text-gray-800">About Me</h2>
                <p class="text-gray-600 mt-2">{{ $bio ?? 'No bio available.' }}</p>
            </x-filament::section>

            <x-filament::section class="p-6 bg-white rounded-lg shadow">
                <h2 class="text-xl font-semibold text-gray-800">Posts</h2>
                <p class="text-gray-600 mt-2">No posts available.</p>
            </x-filament::section>
        </div>

        <!-- Sidebar Section -->
        <div class="space-y-6">

            <x-filament::section class="p-6 bg-white rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Skills</h2>
                
                @if ($skills->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        @foreach ($skills as $skill)
                            <x-filament::badge class="px-3 py-1 text-sm">{{ $skill->name }}</x-filament::badge>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 mt-2">No skills.</p>
                @endif
            </x-filament::section>




            <x-filament::section class="p-6 bg-white rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-800">Assigned Projects</h2>
                @if ($projects->isNotEmpty())
                    <ul class="list-disc pl-5 mt-2 text-gray-700">
                        @foreach ($projects as $project)
                            <li>{{ $project->name }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 mt-2">No projects assigned.</p>
                @endif
            </x-filament::section>

            <x-filament::section class="p-6 bg-white rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-800">Account Settings</h2>
                <ul class="mt-2 space-y-2">
                    <li><a href="#" class="text-blue-600 hover:underline">Reset Password</a></li>
                    <li><a href="#" class="text-blue-600 hover:underline">Manage Browser Sessions</a></li>
                </ul>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
