<div class="flex items-center space-x-2">
    <input 
        type="text" 
        class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" 
        placeholder="Type a message..." 
        wire:model.defer="messageBody"
        wire:keydown.enter="sendMessage"
    >
    <button class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600" wire:click="sendMessage">
        Send
    </button>
</div>