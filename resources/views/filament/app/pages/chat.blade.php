<x-filament-panels::page class="container mx-auto">
    <!-- Chat Messages Section -->
    <div x-data="{ isOpen: window.innerWidth >= 1024 }" class="flex flex-col lg:flex-row gap-4">
        <!-- Collapsible Sidebar -->
        <x-filament::section class=" w-1/3">
            <!-- Toggle Button -->
            <button 
                @click="isOpen = !isOpen" 
                class="lg:hidden flex items-center justify-center p-2 bg-gray-200 rounded-md mb-2"
            >
                <svg x-show="!isOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
                <svg x-show="isOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Sidebar Content -->
            <div 
                x-show="isOpen" 
                x-transition 
                class="lg:block w-full"
            >
                <h2 class="text-xl font-bold mb-4">Conversations</h2>
                <ul class="space-y-2">
                    @foreach ($conversations as $conversation)
                        <li>
                            <button 
                                wire:click="$set('selectedConversationId', {{ $conversation->id }})" 
                                class="w-full text-left px-4 py-2 border border-slate-800 rounded-md {{ $selectedConversationId == $conversation->id ? 'bg-gray-300' : '' }}"
                            >
                                {{ $conversation->name }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </x-filament::section>

        <!-- Chat Section -->
        <x-filament::section class="flex-grow w-full lg:w-3/4">
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
        document.addEventListener('DOMContentLoaded', function () {
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true,
            });

            const channel = pusher.subscribe('conversation.' + {{ $selectedConversationId }});
            channel.bind('MessageSent', function (data) {
                Livewire.emit('messageReceived', data.message);
            });

            channel.bind('UserTyping', function (data) {
                Livewire.emit('userTyping', data);
            });

        });

        document.addEventListener('DOMContentLoaded', function () {
            const chatContainer = document.getElementById('chat-container');

            // Scroll to the bottom initially
            function scrollToBottom() {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }

            // Initial scroll to the bottom
            scrollToBottom();

            Livewire.on('messageReceived', function () {
                setTimeout(scrollToBottom, 100); // Delay to ensure the message is added
            });

            Livewire.on('userTyping', function (user) {
                document.getElementById('typing-indicator').textContent = `${user} is typing...`;
            });
        });


        // Detect typing
        let typingTimeout;
        document.getElementById('messageBody').addEventListener('input', function () {
            clearTimeout(typingTimeout);
            Livewire.emit('userTyping');
            typingTimeout = setTimeout(() => {
                // Stop typing event after 3 seconds of inactivity
                Livewire.emit('userTyping', null);
            }, 3000);
        });
    </script>
</x-filament-panels::page>
