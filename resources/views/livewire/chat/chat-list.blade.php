<div class="p-4 h-full">
    <h2 class="text-lg font-semibold mb-4">Conversations</h2>
    <div class="space-y-2">
        @foreach($conversations as $conversation)
            <div 
                class="p-2 rounded cursor-pointer 
                    {{ $selectedConversationId === $conversation->id ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300' }}"
                wire:click="selectConversation({{ $conversation->id }})"
            >
                {{ $conversation->name }}
            </div>
        @endforeach
    </div>
</div>