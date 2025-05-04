<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\DashboardService;

class ProjectReportController extends Controller
{
    protected $dashboardService;
    
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }
    
    public function download(Request $request)
    {

        // dd($request->all());
        $query = Project::query()
            ->with(['package','headCoordinator','groomCoordinator','brideCoordinator','teams']);
    
        // pull exactly what we sent in the route
        $start  = $request->query('start');  // e.g. "2026-05-01"
        $end    = $request->query('end');    // e.g. "2026-05-29"
        $status = $request->query('status');
    
        if ($status !== null) {
            $query->where('status', $status);
        }
    
        if ($start) {
            // Carbon::parse can handle Y-m-d
            $query->whereDate('end', '>=', Carbon::parse($start));
        }
    
        if ($end) {
            $query->whereDate('end', '<=', Carbon::parse($end));
        }
    
        $projects = $query->get()
            ->each(fn($project) => $project->statusText = $this->getStatusText($project->status));
    
        $pdf = Pdf::loadView('pdf.reports', compact('projects', 'start', 'end', 'status'));
    
        return $pdf->download('overall_project_report.pdf');
    }
    
    
    private function getStatusText($statusCode)
    {
        $statuses = [
            10 => 'Active',
            200 => 'Completed',
            100 => 'Archived',
            0 => 'Canceled',
            50 => 'On Hold',
        ];
        
        return $statuses[$statusCode] ?? 'Unknown';
    }
}