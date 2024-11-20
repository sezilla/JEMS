<x-filament-panels::page>
    <div class="feed-container gap-4 p-4">        
        @foreach ($posts as $post)
            <x-filament::section class="mx-auto mb-6 border rounded p-4">
                <!-- Post Header with Avatar and Date -->
                <div class="flex items-center mb-4">
                    <img src="{{ $post->user->getFilamentAvatarUrl() }}" alt="{{ $post->user->name }}'s avatar" class="w-10 h-10 rounded-full mr-3">
                    <div>
                        <p class="text-lg font-bold">{{ $post->user->name }}</p>
                        <p class="text-gray-500 text-sm">{{ $post->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                <!-- Post Content -->
                <h2 class="text-gray-600">{{ $post->title }}</h2>
                <p class="mb-4">{{ $post->body }}</p>

                <!-- React and Comment Buttons for Post -->
                <div class="flex justify-between items-center border-t pt-2">
                    <button class="text-blue-500 font-semibold hover:underline">React</button>
                    <button class="text-blue-500 font-semibold hover:underline">Comment</button>
                </div>
            </x-filament::section>

            <!-- Comments Section -->
            <div class="comments-section w-2/3 mx-auto ml-6 mt-4">
                @foreach ($post->comments as $comment)
                    <div class="comment mb-4 p-3 border-l">
                        <!-- Comment Header with Avatar and Date -->
                        <div class="flex items-center mb-2">
                            <img src="{{ $comment->user->getFilamentAvatarUrl() }}" alt="{{ $comment->user->name }}'s avatar" class="w-8 h-8 rounded-full mr-2">
                            <div>
                                <span class="font-semibold">{{ $comment->user->name }}</span>
                                <p class="text-gray-500 text-xs">{{ $comment->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        <!-- Comment Content -->
                        <p>{{ $comment->content }}</p>

                        <!-- React and Reply Buttons for Comment -->
                        <div class="flex justify-between items-center mt-2">
                            <button class="text-blue-500 font-semibold hover:underline">React</button>
                            <button class="text-blue-500 font-semibold hover:underline">Reply</button>
                        </div>

                        <!-- Replies Section -->
                        <div class="replies ml-8 mt-2">
                            @foreach ($comment->replies as $reply)
                                <div class="reply p-2 border-l">
                                    <!-- Reply Header with Avatar and Date -->
                                    <div class="flex items-center mb-1">
                                        <img src="{{ $reply->user->getFilamentAvatarUrl() }}" alt="{{ $reply->user->name }}'s avatar" class="w-6 h-6 rounded-full mr-1">
                                        <div>
                                            <span class="font-semibold">{{ $reply->user->name }}</span>
                                            <p class="text-gray-500 text-xs">{{ $reply->created_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                    <!-- Reply Content -->
                                    <p>{{ $reply->content }}</p>

                                    <!-- React Button for Reply -->
                                    <button class="text-blue-500 font-semibold hover:underline mt-2">React</button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
