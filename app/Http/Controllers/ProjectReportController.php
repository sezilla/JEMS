<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ProjectReportController extends Controller
{
    public function download(Request $request)
    {
        $query = Project::with(['package', 'headCoordinator', 'groomCoordinator', 'brideCoordinator', 'teams']);
    
        if ($request->filled('wedding_date_from')) {
            $from = Carbon::createFromFormat('Y-m', $request->wedding_date_from)->startOfMonth();
            $query->whereDate('end', '>=', $from);
        }
    
        if ($request->filled('wedding_date_until')) {
            $until = Carbon::createFromFormat('Y-m', $request->wedding_date_until)->endOfMonth();
            $query->whereDate('end', '<=', $until);
        }
    
        $projects = $query->get();
    
        $pdf = Pdf::loadView('pdf.reports', compact('projects'));
    
        return $pdf->download('overall_project_report.pdf');
    }
    
    
}
