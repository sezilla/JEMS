<!DOCTYPE html>
<html>
<head>
    <title>{{ $project->name }} - Wedding Project PDF</title>
    <style>
        @media print {
            .print-columns {
                column-count: 2;
                column-gap: 40px;
            }

            .section {
                break-inside: avoid; /* prevents breaking inside sections */
                page-break-inside: avoid;
            }

            h1 {
                column-span: all;
            }
        }


        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h1 { text-align: center; font-size: 18px; margin-bottom: 30px; }
        h2 { color: #1f4e79; border-bottom: 1px solid #ccc; padding-bottom: 3px; margin-top: 30px; }
        .section { margin-bottom: 20px; }

        .two-column {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }
        .column {
            flex: 1;
            min-width: 45%;
        }

        .row { margin-bottom: 8px; }
        .label { font-weight: bold; }

        .color-box {
            width: 20px;
            height: 20px;
            display: inline-block;
            border: 1px solid #000;
            vertical-align: middle;
        }

        .photo {
            text-align: right;
            margin-top: 10px;
        }
        .photo img {
            width: 2in;
            height: 3in;
            object-fit: cover;
            border: 1px solid #ccc;
        }

        .checklist input { margin-right: 5px; }

        .agenda, .notes, .emergency {
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .agenda td, .agenda th,
        .emergency td, .emergency th {
            border: 1px solid #aaa;
            padding: 6px;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="print-columns">
    <h1>{{ $project->name }}</h1>

    {{-- Project Details --}}
    <div class="section">
        <h2>Project Details</h2>
        <div class="two-column">
            <div class="column">
                <div class="row"><span class="label">Package:</span> {{ $project->package->name ?? '-' }}</div>
                <div class="row"><span class="label">Start Date:</span> {{ $project->start->format('d/m/Y') }}</div>
                <div class="row"><span class="label">Event Date:</span> {{ $project->end->format('d/m/Y') }}</div>
                <div class="row"><span class="label">Venue:</span> {{ $project->venue }}</div>
                <div class="row"><span class="label">Description:</span> {{ $project->description }}</div>
            </div>
            <div class="column photo">
                @if($project->thumbnail_path)
                    <img src="{{ public_path('storage/' . $project->thumbnail_path) }}" alt="Thumbnail">
                @endif
            </div>
        </div>
    </div>

    {{-- Couple Details --}}
    <div class="section">
        <h2>Couple Details</h2>
        <div class="two-column">
            <div class="column">
                <div class="row"><span class="label">Groom:</span> {{ $project->groom_name }}</div>
                <div class="row"><span class="label">Bride:</span> {{ $project->bride_name }}</div>
                <div class="row"><span class="label">Special Requests:</span> {{ $project->special_request }}</div>
            </div>
            <div class="column">
                <div class="row"><span class="label">Theme Color:</span> <span class="color-box" style="background-color: {{ $project->theme_color }}"></span></div>
            </div>
        </div>
    </div>

    {{-- Coordinators --}}
    <div class="section">
        <h2>Coordinators</h2>
        <div class="two-column">
            <div class="column">
                <div class="row"><span class="label">Groom Coordinator:</span> {{ $project->groomCoordinator->name ?? '-' }}</div>
                <div class="row"><span class="label">Bride Coordinator:</span> {{ $project->brideCoordinator->name ?? '-' }}</div>
                <div class="row"><span class="label">Head Coordinator:</span> {{ $project->headCoordinator->name ?? '-' }}</div>
            </div>
            <div class="column">
                <div class="row"><span class="label">Groom Assistant:</span> {{ optional($project->groom_coor_assistant)->name ?? '-' }}</div>
                <div class="row"><span class="label">Bride Assistant:</span> {{ optional($project->bride_coor_assistant)->name ?? '-' }}</div>
                <div class="row"><span class="label">Head Assistant:</span> {{ optional($project->head_coor_assistant)->name ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Teams --}}
    <div class="section">
        <h2>Teams</h2>
        <div class="two-column">
            <div class="column">
                <div class="row"><span class="label">Catering:</span> {{ optional($project->cateringTeam->first())->name ?? '-' }}</div>
                <div class="row"><span class="label">Hair and Makeup:</span> {{ optional($project->hairAndMakeupTeam->first())->name ?? '-' }}</div>
                <div class="row"><span class="label">Photo and Video:</span> {{ optional($project->photoAndVideoTeam->first())->name ?? '-' }}</div>
            </div>
            <div class="column">
                <div class="row"><span class="label">Designing:</span> {{ optional($project->designingTeam->first())->name ?? '-' }}</div>
                <div class="row"><span class="label">Entertainment:</span> {{ optional($project->entertainmentTeam->first())->name ?? '-' }}</div>
                <div class="row"><span class="label">Coordination:</span> {{ optional($project->coordinationTeam->first())->name ?? '-' }}</div>
            </div>
        </div>
    </div>

    </div>
</body>
</html>
