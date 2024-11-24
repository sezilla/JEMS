<x-filament-panels::page class="container mx-auto">
    <!-- Chat Messages Section -->

    <div class="flex gap-4">
        <x-filament::section class="w-1/4">
            <div class="flex">
                <!-- Sidebar with list of conversations -->
                <div class="w-1/4">
                    <h2 class="text-xl font-bold mb-4">Conversations</h2>
                    <ul class="space-y-2">
                        
                        @foreach ($conversations as $conversation)
                            <li>
                                <x-filament::card>
                                    <button 
                                        wire:click="$set('selectedConversationId', {{ $conversation->id }})" 
                                        class="w-full text-left px-4 py-2 rounded-md {{ $selectedConversationId == $conversation->id }}">
                                        {{ $conversation->name }}
                                    </button>
                                </x-filament::card>
                            </li>
                        @endforeach
                    
                    </ul>
                </div>
            </div>
        </x-filament::section>
        

    
        <x-filament::section class="flex-grow">
            <x-slot name="header">
                <h1 class="text-2xl font-bold">{{ $selectedConversationName }}</h1>
            </x-slot>
            <div id="chat-container" class="h-96 overflow-y-auto p-4">
                @foreach ($messages as $message)
                    <div class="flex mb-4 {{ $message['user_id'] === auth()->id() ? 'justify-end' : '' }}">
                        @if ($message['user_id'] !== auth()->id())
                            <!-- Avatar for Other User -->
                            <x-filament::avatar 
                                src="{{ $message['user']['avatar'] }}" 
                                initials="{{ substr($message['user']['name'], 0, 1) }}" 
                                class="w-10 h-10 mr-2 self-end"
                                size="w-12 h-12"
                            />
                        @endif

                        <div>
                            <div>
                                <strong class="block text-sm">{{ $message['user']['name'] }}</strong>
                            </div>
                            <x-filament::card class="rounded-lg shadow max-w-xs">
                                <p>{{ $message['body'] }}</p>
                            </x-filament::card>
                            <span class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($message['created_at'])->format('h:i A, M d') }}
                            </span>
                        </div>

                        @if ($message['user_id'] === auth()->id())
                            <!-- Avatar for Current User -->
                            <x-filament::avatar 
                                src="{{ auth()->user()->getFilamentAvatarUrl() ?? asset('images/default-avatar.png') }}" 
                                initials="{{ substr(auth()->user()->name, 0, 1) }}" 
                                class="w-10 h-10 ml-2 self-end"
                                size="w-12 h-12"
                            />
                        @endif
                    </div>
                @endforeach
            </div>
            <!-- Message Input Form -->
            <div class="mt-4">
                {{ $this->form }}
                <div class="flex justify-end pt-4">
                    <x-filament::button wire:click="submit" type="submit">
                        {{ __('Send') }}
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
    </div>

    <!-- Pusher Script for Real-time Updates -->
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        });

        const channel = pusher.subscribe(`conversation.${@this.selectedConversationId}`);
        channel.bind('MessageSent', function(data) {
            Livewire.emit('messageReceived', data.message);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true,
            });

            const channel = pusher.subscribe(`conversation.{{ $selectedConversationId }}`);
            channel.bind('MessageSent', function (data) {
                // Emit the Livewire event to add the message
                Livewire.emit('messageReceived', data.message);
            });
        });
    </script>
    <script>
        document.addEventListener('livewire:load', function () {
            const chatContainer = document.getElementById('chat-container');
    
            // Function to scroll to the bottom
            function scrollToBottom() {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
    
            // Scroll to the bottom when the page loads
            scrollToBottom();
    
            // Listen for new messages and scroll to the bottom
            Livewire.on('messageReceived', function () {
                setTimeout(scrollToBottom, 100); // Allow time for DOM updates
            });
        });
    </script>
    <script>
        window.addEventListener('scroll-to-bottom', function () {
            const chatContainer = document.getElementById('chat-container');
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });
    </script>
    
    

</x-filament-panels::page>
