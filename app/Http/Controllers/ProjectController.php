<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PythonService;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Project;

class ProjectController extends Controller
{
    protected $pythonService;

    public function __construct(PythonService $pythonService)
    {
        $this->pythonService = $pythonService;
    }

    //hmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm
    public function classifyTask(Request $request) {}

    public function exportPdf($id)
    {
        $project = Project::with('user', 'teams')->findOrFail($id);

        $pdf = Pdf::loadView('pdf.project', compact('project'));

        return $pdf->download("Project_{$project->id}.pdf");
    }
}
