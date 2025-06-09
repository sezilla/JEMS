<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Enums\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ProjectReportController extends Controller
{
    public function download(Request $request)
    {
        $query = Project::query()
            ->with(['package', 'headCoordinator', 'groomCoordinator', 'brideCoordinator', 'teams']);

        $start  = $request->query('start');
        $end    = $request->query('end');
        $status = $request->query('status');

        if ($status !== null) {
            $statusInt = config('project.project_status')[$status] ?? null;

            if ($statusInt !== null) {
                $query->where('status', $statusInt);
            }
        }

        if ($start) {
            $query->whereDate('end', '>=', Carbon::parse($start));
        }

        if ($end) {
            $query->whereDate('end', '<=', Carbon::parse($end));
        }

        $projects = $query->get();

        $pdf = Pdf::loadView('pdf.reports', compact('projects', 'start', 'end', 'status'));
        
        // Configure DomPDF options
        $pdf->getDomPDF()->set_option('enable_remote', true);
        
        $date = now()->format('Y-m-d');
        return $pdf->download("JEM_Event_Report_{$date}.pdf");
    }
}
