<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Overall Reports of Projects</title>

    <style>
        /* Reset & base */
        body { margin: 0; padding: 0; font-family: sans-serif; font-size: 12px; color: #333; }
        .container { padding: 20px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header .title { font-size: 18px; font-weight: bold; }
        .header img.logo { height: 40px; }

        /* Summary section */
        .summary { margin-bottom: 30px; }
        .summary-table { width: 100%; border: none; margin-top: 5px; }
        .summary-table td { padding: 4px 8px; }
        .summary .label { font-weight: bold; }

        /* Main data table */
        table.main-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.main-table th,
        table.main-table td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        table.main-table th { background-color: #f86b84; color: #fff; }

        /* Footer */
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with title + logo -->
        <div class="header">
            <div class="title">Project Reports</div>
            <h1>Jhossa Events Management</h1>
        </div>

        <!-- Summary block: date range, filters, etc. -->
        <div class="summary">
            <p><strong>Date Range:</strong>
                {{ $start ? \Carbon\Carbon::parse($start)->format('F j, Y') : 'N/A' }}
                &mdash;
                {{ $end ? \Carbon\Carbon::parse($end)->format('F j, Y') : 'N/A' }}
            </p>
            @if($status)
                <p><strong>Status Filter:</strong> {{ ucfirst($status) }}</p>
            @endif

        </div>

        <!-- Main data table -->
        <table class="main-table">
    <thead>
        <tr>
            <th>Project Name</th>
            <th>Package Name</th>
            <th>Location</th>
            <th>Wedding Date</th>
            <th>Head Coordinator</th>
            <th>Status</th>
            <th>Teams Assigned</th>
            <th>Task Per Department Progress</th>
            <th>Overall Progress</th>
        </tr>
    </thead>
    <tbody>
        @foreach($projects as $project)
        <tr>
            <td>{{ $project->name }}</td>
            <td>
                {{ $project->package->name ?? 'N/A' }}<br>
                <small>
                    @php
                        $prices = [
                            'Ruby' => '130,000 Php',
                            'Garnet' => '165,000 Php',
                            'Emerald' => '190,000 Php',
                            'Infinity' => '250,000 Php',
                            'Sapphire' => '295,000 Php',
                        ];
                    @endphp
                    {{ $prices[$project->package->name] ?? 'N/A' }}
                </small>
            </td>
            <td>{{ $project->venue }}</td>
            <td>
                {{ \Carbon\Carbon::parse($project->end)->format('F j, Y') }}<br>
                <small>started at {{ \Carbon\Carbon::parse($project->start)->format('F j, Y') }}</small><br>
                
            </td>
            <td>
                {{ $project->headCoordinator->name ?? '-' }}<br>
                <small>{{ $project->groomCoordinator->name ?? '-' }} & {{ $project->brideCoordinator->name ?? '-' }}</small>
            </td>
            <td>{{ $project->statusText }}</td>
            <td>
                @foreach($project->teams as $team)
                    {{ $team->name }}<br>
                @endforeach
            </td>
            <td>
                @php
                    $trelloService = app(\App\Services\TrelloTask::class);
                    $listId = $trelloService->getBoardDepartmentsListId($project->trello_board_id);
                    
                    if ($listId) {
                        $cards = $trelloService->getListCards($listId);
                        $progress = [];
                        
                        foreach ($cards as $card) {
                            $checklists = $trelloService->getCardChecklists($card['id']);
                            $totalTasks = 0;
                            $completedTasks = 0;
                            
                            foreach ($checklists as $checklist) {
                                $items = $trelloService->getChecklistItems($checklist['id']);
                                $totalTasks += count($items);
                                $completedTasks += count(array_filter($items, fn($item) => ($item['state'] ?? 'incomplete') === 'complete'));
                            }
                            
                            if ($totalTasks === 0) {
                                $progress[] = $card['name'] . ': No tasks';
                                continue;
                            }
                            
                            $percentage = round(($completedTasks / $totalTasks) * 100);
                            $color = $percentage >= 80 ? 'green' : ($percentage >= 50 ? 'orange' : 'red');
                            $progress[] = "<span style='color: {$color}'>{$card['name']}: {$percentage}%</span>";
                        }
                        
                        echo implode("<br>", $progress);
                    } else {
                        echo 'No Trello board found';
                    }
                @endphp
            </td>
            <td>
                @php
                    if ($listId) {
                        $cards = $trelloService->getListCards($listId);
                        $totalTasks = 0;
                        $completedTasks = 0;
                        
                        foreach ($cards as $card) {
                            $checklists = $trelloService->getCardChecklists($card['id']);
                            
                            foreach ($checklists as $checklist) {
                                $items = $trelloService->getChecklistItems($checklist['id']);
                                $totalTasks += count($items);
                                $completedTasks += count(array_filter($items, fn($item) => ($item['state'] ?? 'incomplete') === 'complete'));
                            }
                        }
                        
                        if ($totalTasks === 0) {
                            echo 'No tasks found';
                        } else {
                            $percentage = round(($completedTasks / $totalTasks) * 100);
                            $color = $percentage >= 80 ? 'green' : ($percentage >= 50 ? 'orange' : 'red');
                            echo "<span style='color: {$color}'>{$percentage}%</span>";
                        }
                    } else {
                        echo 'No Trello board found';
                    }
                @endphp
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

    </div>

    <!-- Fixed footer with page number and generated by -->
    <div class="footer">
        Report generated by {{ auth()->user()->name }} | {{ now()->format('F j, Y, H:i') }} | JEM <span class="page"></span>
    </div>
</body>
</html>
