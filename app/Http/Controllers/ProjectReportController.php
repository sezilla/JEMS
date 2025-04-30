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
        $query = Project::query()
                ->with(['package', 'headCoordinator', 'groomCoordinator', 'brideCoordinator', 'teams']);
        
        $wedding_date_from = $request->query('wedding_date_from');
        $wedding_date_until = $request->query('wedding_date_until');
        $status = $request->query('status');
        
        if ($status !== null) {
            $query->where('status', $status);
        }
        
        if ($wedding_date_from) {
            $from = Carbon::createFromFormat('Y-m', $wedding_date_from)->startOfMonth();
            $query->whereDate('end', '>=', $from);
        }
        
        if ($wedding_date_until) {
            $until = Carbon::createFromFormat('Y-m', $wedding_date_until)->endOfMonth();
            $query->whereDate('end', '<=', $until);
        }
        
        $projects = $query->get();
        
        foreach ($projects as $project) {
            $project->statusText = $this->getStatusText($project->status);
        }
        
        $pdf = Pdf::loadView('pdf.reports', compact('projects'));
        
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