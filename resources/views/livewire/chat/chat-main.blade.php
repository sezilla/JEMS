<div class="flex h-96 bg-gray-100">
    <div class="w-1/3 bg-white border-r border-gray-700">
        @livewire('chat.chat-list')
    </div>
    <div class="w-2/3 flex flex-1 flex-col bg-white">
        <div class="flex-1 overflow-y-auto p-4">
            @livewire('chat.chat-box', ['selectedConversationId' => $selectedConversationId])
        </div>
        <div class="border-t bg-gray-50 p-3 sticky bottom-0">
            @livewire('chat.send-message', ['selectedConversationId' => $selectedConversationId])
        </div>
    </div>
</div>
