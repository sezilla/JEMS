<!DOCTYPE html>
<html>
<head>
    <title>{{ $project->name }} - Wedding Event PDF</title>
    <style>
        @page {
            margin: 0.4in;
            size: A4;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 100%;
            display: flex;
            flex-direction: column;
        }
        
        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #f86b84;
            padding-bottom: 8px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #1f4e79;
            margin: 0 0 3px 0;
            letter-spacing: 0.5px;
        }
        
        .header .subtitle {
            font-size: 10px;
            color: #666;
            margin: 0;
        }
        
        /* Main Content Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            flex: 1;
        }
        
        /* Left Column */
        .left-column {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        /* Right Column */
        .right-column {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        /* Section Styling */
        .section {
            background: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 8px;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1f4e79;
            margin: 0 0 6px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
        }
        
        /* Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table tr {
            border-bottom: 1px solid #eee;
        }
        
        .info-table tr:last-child {
            border-bottom: none;
        }
        
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            font-weight: bold;
            width: 35%;
            color: #555;
        }
        
        .info-table td:last-child {
            color: #333;
        }
        
        /* Color Box */
        .color-box {
            width: 14px;
            height: 14px;
            display: inline-block;
            border: 1px solid #999;
            border-radius: 2px;
            vertical-align: middle;
        }
        
        /* Teams Grid */
        .teams-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            margin-top: 4px;
        }
        
        .team-item {
            background: white;
            padding: 4px;
            border-radius: 3px;
            border: 1px solid #e8e8e8;
            font-size: 9px;
        }
        
        .team-label {
            font-weight: bold;
            color: #f86b84;
            display: block;
            margin-bottom: 1px;
        }
        
        .team-value {
            color: #666;
        }
        
        /* Coordinators Grid */
        .coordinator-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
            margin-top: 4px;
        }
        
        .coordinator-item {
            background: white;
            padding: 3px 4px;
            border-radius: 3px;
            border-left: 2px solid #f86b84;
            font-size: 9px;
        }
        
        .coordinator-label {
            font-weight: bold;
            color: #555;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .coordinator-value {
            color: #333;
            margin-top: 1px;
        }
        
        /* Thumbnail */
        .thumbnail {
            text-align: center;
            margin-bottom: 6px;
        }
        
        .thumbnail img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #f86b84;
        }
        
        /* Special styling for longer content */
        .description-text {
            font-size: 9px;
            line-height: 1.3;
            color: #666;
            max-height: 45px;
            overflow: hidden;
        }
        
        /* Footer */
        .footer {
            margin-top: 8px;
            text-align: center;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if($project->thumbnail_path)
                <div class="thumbnail">
                    <img src="{{ public_path('storage/' . $project->thumbnail_path) }}" alt="Project Thumbnail">
                </div>
            @endif
            <h1>{{ $project->name }}</h1>
            <p class="subtitle">Wedding Event Overview</p>
        </div>
        
        <!-- Main Content Grid -->
        <div class="main-grid">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Event Details -->
                <div class="section">
                    <h3 class="section-title">Event Details</h3>
                    <table class="info-table">
                        <tr>
                            <td>Date:</td>
                            <td>{{ $project->end->format('F j, Y') }}</td>
                        </tr>
                        <tr>
                            <td>Venue:</td>
                            <td>{{ $project->venue ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td>Package:</td>
                            <td>{{ $project->package->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Legend:</td>
                            <td><span class="color-box" style="background-color: {{ $project->theme_color ?: '#ffffff' }}"></span> {{ $project->theme_color ?: 'Not specified' }}</td>
                        </tr>
                    </table>
                </div>
                
                <!-- Couple Information -->
                <div class="section">
                    <h3 class="section-title">Couple Information</h3>
                    <table class="info-table">
                        <tr>
                            <td>Groom:</td>
                            <td>{{ $project->groom_name ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td>Bride:</td>
                            <td>{{ $project->bride_name ?: '-' }}</td>
                        </tr>
                    </table>
                </div>
                
                <!-- Special Requests -->
                @if($project->special_request)
                <div class="section">
                    <h3 class="section-title">Special Requests</h3>
                    <div class="description-text">{{ $project->special_request }}</div>
                </div>
                @endif
                
                <!-- Description -->
                @if($project->description)
                <div class="section">
                    <h3 class="section-title">Description</h3>
                    <div class="description-text">{{ $project->description }}</div>
                </div>
                @endif
            </div>
            
            <!-- Right Column -->
            <div class="right-column">
                <!-- Coordinators -->
                <div class="section">
                    <h3 class="section-title">Coordination Team</h3>
                    <div class="coordinator-grid">
                        <div class="coordinator-item">
                            <div class="coordinator-label">Head Coordinator</div>
                            <div class="coordinator-value">{{ $project->headCoordinator->name ?? '-' }}</div>
                        </div>
                        <div class="coordinator-item">
                            <div class="coordinator-label">Head Assistant</div>
                            <div class="coordinator-value">{{ $project->headAssistant->name ?? '-' }}</div>
                        </div>
                        <div class="coordinator-item">
                            <div class="coordinator-label">Groom Coordinator</div>
                            <div class="coordinator-value">{{ $project->groomCoordinator->name ?? '-' }}</div>
                        </div>
                        <div class="coordinator-item">
                            <div class="coordinator-label">Groom Assistant</div>
                            <div class="coordinator-value">{{ $project->groomAssistant->name ?? '-' }}</div>
                        </div>
                        <div class="coordinator-item">
                            <div class="coordinator-label">Bride Coordinator</div>
                            <div class="coordinator-value">{{ $project->brideCoordinator->name ?? '-' }}</div>
                        </div>
                        <div class="coordinator-item">
                            <div class="coordinator-label">Bride Assistant</div>
                            <div class="coordinator-value">{{ $project->brideAssistant->name ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Service Teams -->
                <div class="section">
                    <h3 class="section-title">Service Teams</h3>
                    <div class="teams-grid">
                        <div class="team-item">
                            <span class="team-label">Catering</span>
                            <span class="team-value">{{ optional($project->cateringTeam->first())->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="team-item">
                            <span class="team-label">Hair & Makeup</span>
                            <span class="team-value">{{ optional($project->hairAndMakeupTeam->first())->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="team-item">
                            <span class="team-label">Photo & Video</span>
                            <span class="team-value">{{ optional($project->photoAndVideoTeam->first())->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="team-item">
                            <span class="team-label">Designing</span>
                            <span class="team-value">{{ optional($project->designingTeam->first())->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="team-item">
                            <span class="team-label">Entertainment</span>
                            <span class="team-value">{{ optional($project->entertainmentTeam->first())->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="team-item">
                            <span class="team-label">Coordination</span>
                            <span class="team-value">{{ optional($project->coordinationTeam->first())->name ?? 'Not assigned' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Project Status -->
                <div class="section">
                    <h3 class="section-title">Project Status</h3>
                    <table class="info-table">
                        <tr>
                            <td>Status:</td>
                            <td>{{ $project->status->value ?? 'Active' }}</td>
                        </tr>
                        <tr>
                            <td>Created:</td>
                            <td>{{ $project->created_at->format('M j, Y') }}</td>
                        </tr>
                        <tr>
                            <td>Project ID:</td>
                            <td>#{{ $project->id }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            Generated on {{ now()->format('F j, Y \a\t H:i') }} | JEMS powered by <a href="https://jems.com" style="color: #f86b84; text-decoration: none;">DDDM</a>
        </div>
    </div>
</body>
</html>