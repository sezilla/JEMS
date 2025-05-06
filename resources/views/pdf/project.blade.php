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
        h2 { color: #f86b84; border-bottom: 1px solid #ccc; padding-bottom: 3px; margin-top: 30px; }
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
            background-color: var(--theme-color, #ffffff);
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
    <div class="container">
        <!-- Project Photo at Top -->
        @if($project->thumbnail_path)
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="{{ public_path('storage/' . $project->thumbnail_path) }}" alt="Thumbnail" style="width: 140px; height: 140px; object-fit: cover; border: 1px solid #ccc; border-radius: 8px;">
            </div>
        @endif

        <!-- Two Column Details -->
        <div style="display: flex; gap: 40px; margin-bottom: 30px;">
            <!-- Left Column: Project Meta -->
            <div style="flex: 1; min-width: 220px;">
                <h1 style="font-family: 'Georgia', serif; font-size: 28px; margin: 0 0 16px 0; font-weight: bold; letter-spacing: 1px;">EVENT DETAILS</h1>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr><td style="font-weight: bold; width: 110px; padding: 6px 8px;">Project Name:</td><td style="padding: 6px 8px;">{{ $project->name }}</td></tr>
                    <tr><td style="font-weight: bold; padding: 6px 8px;">Date:</td><td style="padding: 6px 8px;">{{ $project->end->format('F j, Y') }}</td></tr>
                    <tr><td style="font-weight: bold; padding: 6px 8px;">Time:</td><td style="padding: 6px 8px;">{{ $project->start->format('H:i') }}</td></tr>
                    <tr><td style="font-weight: bold; padding: 6px 8px;">Location:</td><td style="padding: 6px 8px;">{{ $project->venue }}</td></tr>
                    <tr><td style="font-weight: bold; padding: 6px 8px;">Package:</td><td style="padding: 6px 8px;">{{ $project->package->name ?? '-' }}</td></tr>
                    <tr><td style="font-weight: bold; padding: 6px 8px;">Description:</td><td style="padding: 6px 8px;">{{ $project->description }}</td></tr>
                </table>
            </div>
            <!-- Right Column: Couple & Coordinators -->
            <div style="flex: 1; min-width: 220px;">
                <h2 style="font-size: 18px; color: #1f4e79; margin-bottom: 10px;">Couple Details</h2>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
                    <tr><td style="font-weight: bold; width: 110px; padding: 6px 8px;">Groom:</td><td style="padding: 6px 8px;">{{ $project->groom_name }}</td></tr>
                    <tr><td style="font-weight: bold; padding: 6px 8px;">Bride:</td><td style="padding: 6px 8px;">{{ $project->bride_name }}</td></tr>
                    <tr><td style="font-weight: bold; padding: 6px 8px;">Special Requests:</td><td style="padding: 6px 8px;">{{ $project->special_request }}</td></tr>
                    <tr><td style="font-weight: bold; padding: 6px 8px;">Theme Color:</td><td style="padding: 6px 8px;"><span class="color-box" style="background-color: {{ $project->theme_color }}"></span></td></tr>
                </table>
                <h2 style="font-size: 18px; color: #1f4e79; margin-bottom: 10px;">Coordinators</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr><td style="font-weight: bold; width: 110px; padding: 6px 8px;">Groom Coordinator:</td><td style="padding: 6px 8px;">{{ $project->groomCoordinator->name ?? '-' }}</td></tr>
                    <tr><td style="font-weight: bold; padding: 6px 8px;">Bride Coordinator:</td><td style="padding: 6px 8px;">{{ $project->brideCoordinator->name ?? '-' }}</td></tr>
                    <tr><td style="font-weight: bold; padding: 6px 8px;">Head Coordinator:</td><td style="padding: 6px 8px;">{{ $project->headCoordinator->name ?? '-' }}</td></tr>
                    <tr><td style="font-weight: bold; padding: 6px 8px;">Groom Assistant:</td><td style="padding: 6px 8px;">{{ optional($project->groom_coor_assistant)->name ?? '-' }}</td></tr>
                    <tr><td style="font-weight: bold; padding: 6px 8px;">Bride Assistant:</td><td style="padding: 6px 8px;">{{ optional($project->bride_coor_assistant)->name ?? '-' }}</td></tr>
                    <tr><td style="font-weight: bold; padding: 6px 8px;">Head Assistant:</td><td style="padding: 6px 8px;">{{ optional($project->head_coor_assistant)->name ?? '-' }}</td></tr>
                </table>
            </div>
        </div>

        <!-- Teams Section (unchanged) -->
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
