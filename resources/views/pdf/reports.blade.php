<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>
        @php
            $title = match($status) {
                'completed' => 'Completed Events',
                'active' => 'Active Events',
                'archived' => 'Archived Events',
                'onhold' => 'On Hold Events',
                'cancelled' => 'Cancelled Events',
                default => 'Overall Events Report'
            };
        @endphp
        {{ $title }}
    </title>

    <style>
        @page {
            size: landscape;
            margin: 10mm 15mm;
        }
        /* Reset & base */
        body { 
            margin: 0; 
            padding: 0; 
            font-family: sans-serif; 
            font-size: 12px; 
            color: #333;
        }
        .container { 
            padding: 20px;
            width: 100%;
            max-width: 100%;
        }

        /* Header - University style layout */
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            width: 100%;
        }
        .header-content {
            display: inline-block;
        }
        .logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 8px;
        }
        .organization-info {
            font-size: 12px;
            color: #333;
            line-height: 1.3;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .address {
            font-size: 10px;
            color: #333;
            line-height: 1.2;
            margin-bottom: 5px;
        }
        .user-info {
            font-size: 9px;
            color: #333;
            line-height: 1.2;
        }

        /* Title Section */
        .title-section {
            text-align: center;
            margin: 10px 0 15px 0;
            padding: 10px 0;
            border-top: 2px solid #f86b84;
            border-bottom: 2px solid #f86b84;
        }
        .main-title {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Summary section - cleaned up to match header style */
        .summary { 
            text-align: center;
            margin-bottom: 15px;
            padding: 0;
        }
        .summary p {
            margin: 3px 0;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
        }
        .summary strong {
            font-weight: bold;
            color: #333;
        }

        /* Main data table */
        table.main-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px;
            table-layout: fixed;
        }
        table.main-table th,
        table.main-table td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left;
            word-wrap: break-word;
        }
        table.main-table th { 
            background-color: #f86b84; 
            color: #fff;
            font-weight: bold;
        }

        /* Footer */
        .footer { 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            right: 0; 
            text-align: center; 
            font-size: 10px; 
            color: #333; 
            border-top: 1px solid #ddd; 
            padding: 5px 0;
            width: 100%;
        }

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

        /* Page number styling */
        .page-number {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with logo centered -->
       <!-- Header with logo and info in two columns -->
       <div class="header">
            <table style="width: 100%; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 180px; text-align: center; vertical-align: middle; border: none; padding: 0;">
                        <img src="{{ public_path('images/logo.webp') }}" alt="Logo" style="max-width: 160px; height: auto; margin-bottom: 5px;">
                        <div style="font-size: 10px; color: #B0B0B0; margin-top: 3px;">
                            Jhossa Events Management
                        </div>
                    </td>
                    <td style="text-align: left; vertical-align: middle; border: none; padding-left: 20px;">
                        <div class="organization-info">
                            JHOSSA EVENTS MANAGEMENT<br>
                            Event Planning & Coordination Services
                        </div>
                        <div class="address">
                            3rd Flr. Jhossa Event Management Bldg, Molino Blvd, Bacoor Cavite, Bacoor, Philippines
                        </div>
                        <div class="address">
                            Phone: 0977 385 5525 | Email: jhossaeventmanagement.com
                        </div>
                        <div class="user-info">
                            Report Generated by: {{ auth()->user()->name }} | Date: {{ now()->format('F j, Y, H:i') }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Title Section -->
        <div class="title-section">
            <h1 class="main-title">{{ $title }}</h1>
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
        Report generated by {{ auth()->user()->name }} | {{ now()->format('F j, Y, H:i') }} | JEM
    </div>
</body>
</html>