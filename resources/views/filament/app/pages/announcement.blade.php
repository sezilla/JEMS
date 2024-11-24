<x-filament::page>
    <div class="space-y-4">
        <form wire:submit.prevent="submitPost">
            {{ $this->form }}
        </form>
        
        @foreach ($posts as $post)
            <div class="p-4 bg-white shadow rounded-lg space-y-2">
                <div class="flex items-center space-x-2">
                    <img src="{{ $post->user->avatar_url }}" class="w-10 h-10 rounded-full" alt="Avatar">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $post->user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <p>{{ $post->body }}</p>
                
                <div class="space-y-2">
                    @foreach ($post->comments as $comment)
                        <div class="ml-4 p-2 bg-gray-100 rounded-lg space-y-2">
                            <div class="flex items-center space-x-2">
                                <img src="{{ $comment->user->avatar_url }}" class="w-8 h-8 rounded-full" alt="Avatar">
                                <div>
                                    <h4 class="text-sm font-semibold">{{ $comment->user->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <p>{{ $comment->content }}</p>
                            
                            @foreach ($comment->replies as $reply)
                                <div class="ml-4 p-2 bg-white rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <img src="{{ $reply->user->avatar_url }}" class="w-6 h-6 rounded-full" alt="Avatar">
                                        <div>
                                            <h5 class="text-sm font-semibold">{{ $reply->user->name }}</h5>
                                            <p class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <p>{{ $reply->content }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <form wire:submit.prevent="submitComment({{ $post->id }})">
                    <input type="text" class="mt-2 p-2 border border-gray-300 rounded-lg w-full" placeholder="Add a comment..." wire:model.defer="newComment.{{ $post->id }}">
                </form>
            </div>
        @endforeach
    </div>

    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true
        });

        const channel = pusher.subscribe('private-posts');
        channel.bind('App\\Events\\PostCreated', function(data) {
            Livewire.emit('postAdded');
        });
    </script>
</x-filament::page>
