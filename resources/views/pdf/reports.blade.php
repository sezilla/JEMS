<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Overall Reports of Projects</title>

    <style>
        @page {
            size: landscape;
            margin: 10mm;
        }
        /* Reset & base */
        body { margin: 0; padding: 0; font-family: sans-serif; font-size: 12px; color: #333; }
        .container { padding: 20px; }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .main-title {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }
        .subtitle {
            font-size: 14px;
            color: #888;
            margin-top: 4px;
            font-weight: normal;
            letter-spacing: 1px;
        }
        .header img.logo { height: 40px; }
        .subtext {
            font-size: 11px;
            color: #bbb;
            margin-top: 2px;
            font-style: italic;
            letter-spacing: 0.5px;
        }

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

        /* Status colors */
        .status-info { color: #3b82f6; }
        .status-primary { color: #6366f1; }
        .status-warning { color: #f59e0b; }
        .status-danger { color: #ef4444; }
        .status-secondary { color: #64748b; }

        /* Progress colors */
        .progress-red { color: #ef4444; }
        .progress-amber { color: #f59e0b; }
        .progress-green { color: #10b981; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with title + logo -->
        <div class="header">
            <h1 class="main-title">Overall Reports of Projects</h1>
            <div class="subtitle">Jhossa Events Management</div>
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
                    <td>{{ $project->name ?? 'No Project Name' }}</td>
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
                            {{ $prices[$project->package->name] ?? 'Price Not Available' }}
                        </small>
                    </td>
                    <td>{{ $project->venue ?? 'No Venue' }}</td>
                    <td>
                        {{ $project->end ? \Carbon\Carbon::parse($project->end)->format('F j, Y') : 'No End Date' }}<br>
                        <small>started at {{ $project->start ? \Carbon\Carbon::parse($project->start)->format('F j, Y') : 'No Start Date' }}</small>
                    </td>
                    <td>
                        {{ $project->headCoordinator->name ?? 'No Head Coordinator' }}<br>
                        <small>{{ $project->groomCoordinator->name ?? 'No Groom Coordinator' }} & {{ $project->brideCoordinator->name ?? 'No Bride Coordinator' }}</small>
                    </td>
                    <td>
                        @php
                            $status = $project->status instanceof \App\Enums\ProjectStatus
                                ? $project->status
                                : \App\Enums\ProjectStatus::tryFrom((int) $project->status);
                            
                            $statusClass = match($status) {
                                \App\Enums\ProjectStatus::ACTIVE => 'status-info',
                                \App\Enums\ProjectStatus::COMPLETED => 'status-primary',
                                \App\Enums\ProjectStatus::ARCHIVED => 'status-warning',
                                \App\Enums\ProjectStatus::CANCELLED => 'status-danger',
                                \App\Enums\ProjectStatus::ON_HOLD => 'status-secondary',
                                default => ''
                            };
                        @endphp
                        <span class="{{ $statusClass }}">{{ $status?->label() ?? 'Unknown' }}</span>
                    </td>
                    <td>
                        @php
                            $teams = $project->teams->pluck('name')->toArray();
                            if ($project->package->name === 'Ruby') {
                                $teams = array_filter($teams, function ($team) {
                                    return $team !== 'Photo&Video';
                                });
                            }
                            echo implode("<br>", $teams);
                        @endphp
                    </td>
                    <td>
                        @php
                            $percentages = app(\App\Services\DashboardService::class)->getCardCompletedPercentage($project->id);
                            echo $percentages;
                        @endphp
                    </td>
                    <td>
                        @php
                            $percentages = app(\App\Services\ProjectService::class)->getProjectProgress($project);
                            
                            if (empty($percentages)) {
                                echo 'No data available';
                            } else {
                                $total = array_sum($percentages);
                                $count = count($percentages);
                                $average = $count > 0 ? round($total / $count) : 0;
                                
                                $progressClass = 'progress-green';
                                if ($average < 30) {
                                    $progressClass = 'progress-red';
                                } elseif ($average < 70) {
                                    $progressClass = 'progress-amber';
                                }
                                
                                echo "<span class='{$progressClass}' style='font-weight: bold;'>{$average}%</span>";
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
