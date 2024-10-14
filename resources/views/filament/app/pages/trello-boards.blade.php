<x-filament-panels::page>
    <h1>Trello Boards</h1>
    <ul>
        @foreach($boards as $board)
            <li>{{ $board['name'] }}</li>
        @endforeach
    </ul>
</x-filament-panels::page>



 