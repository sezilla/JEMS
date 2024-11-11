<x-filament::section x-data="{ showDirect: true, searchQuery: '' }">
    <!-- Heading -->
    <h1 class="text-2xl font-bold mb-4">Contacts</h1>
    
    <!-- Search Bar -->
    <div class="mb-4">
        <input
            type="text"
            placeholder="Search contacts..."
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
            x-model="searchQuery"
        />
    </div>
    
    <!-- Contact Categories Tabs -->
    <div class="flex justify-center gap-4 mb-6">
        <a 
            href="#" 
            @click.prevent="showDirect = true"
            :class="{ 'text-blue-500 font-semibold': showDirect }"
            class="hover:text-blue-500"
        >
            Direct
        </a>
        <a 
            href="#" 
            @click.prevent="showDirect = false"
            :class="{ 'text-blue-500 font-semibold': !showDirect }"
            class="hover:text-blue-500"
        >
            Groups
        </a>
    </div>

    <!-- Direct Contacts Section -->
    <div x-show="showDirect" class="mb-6">
        <h2 class="text-lg font-semibold mb-2">Direct Contacts</h2>
        <ul class="space-y-2">
            @foreach ($directContacts as $contact)
                <li>{{ $contact->name }}</li>
            @endforeach
        </ul>
    </div>

    <!-- Group Contacts Section -->
    <div x-show="!showDirect" class="mb-6">
        <h2 class="text-lg font-semibold mb-2">Group Contacts</h2>
        <ul class="space-y-2">
            @foreach ($groupChats as $group)
                <li>{{ $group->name }}</li>
            @endforeach
        </ul>
    </div>
</x-filament::section>
