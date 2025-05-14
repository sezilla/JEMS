<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Enums\ProjectStatus;
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

        $projects = $query->get()
            ->each(fn($project) => $project->statusText = $this->getStatusText($project->status));

        $pdf = Pdf::loadView('pdf.reports', compact('projects', 'start', 'end', 'status'));

        return $pdf->download('overall_project_report.pdf');
    }


    private function getStatusText($statusCode)
    {
        if ($statusCode instanceof ProjectStatus) {
            return $statusCode->label();
        }

        $enum = ProjectStatus::tryFrom($statusCode);
        return $enum ? $enum->label() : 'Unknown';
    }
}
