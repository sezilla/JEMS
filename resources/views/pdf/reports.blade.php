<!DOCTYPE html>
<html>
<head>
    <title>Project Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h2>Overall Reports of Projects</h2>

    <p><strong>Date Range:</strong>
    {{ $start
        ? \Carbon\Carbon::parse($start)->format('F j, Y')
        : 'N/A'
    }}
    &mdash;
    {{ $end
        ? \Carbon\Carbon::parse($end)->format('F j, Y')
        : 'N/A'
    }}
    </p>


    <table>
        <thead>
            <tr>
                <th>Project Name</th>
                <th>Package</th>
                <th>Price</th>
                <th>Venue</th>
                <th>Start</th>
                <th>Wedding Date</th>
                <th>Head Coord.</th>
                <th>Groom Coord.</th>
                <th>Bride Coord.</th>
                <th>Status</th>
                <th>Teams</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $project)
                <tr>
                    <td>{{ $project->name }}</td>
                    <td>{{ $project->package->name ?? 'N/A' }}</td>
                    <td>
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
                    </td>
                    <td>{{ $project->venue }}</td>
                    <td>{{ \Carbon\Carbon::parse($project->start)->format('F j, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($project->end)->format('F j, Y') }}</td>
                    <td>{{ $project->headCoordinator->name ?? '-' }}</td>
                    <td>{{ $project->groomCoordinator->name ?? '-' }}</td>
                    <td>{{ $project->brideCoordinator->name ?? '-' }}</td>
                    <td>
                        @php
                            $statuses = [
                                10 => 'Active', 200 => 'Completed', 100 => 'Archived', 0 => 'Canceled', 50 => 'On Hold'
                            ];
                        @endphp
                        {{ $statuses[$project->status] ?? 'Unknown' }}
                    </td>
                    <td>
                        @php
                            $teams = $project->teams->pluck('name')->toArray();
                            if ($project->package->name === 'Ruby') {
                                $teams = array_filter($teams, fn($team) => $team !== 'Photo&Video');
                            }
                        @endphp
                        {{ implode(', ', $teams) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
