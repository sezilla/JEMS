<div class="p-4 h-full flex flex-col">
    <div class="flex-1 overflow-y-auto space-y-2 p-2">
        @foreach($messages as $message)
            <div class="p-2 {{ $message['user_id'] === auth()->id() ? 'bg-gray-300 self-end' : 'bg-blue-500 text-white self-start' }} rounded w-fit">
                {{ $message['body'] }}
            </div>
        @endforeach
    </div>
</div>