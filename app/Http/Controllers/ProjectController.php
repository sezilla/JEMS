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

    /**
     * Allocate teams for a project.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allocateTeams(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'project_name' => 'required|string',
            'package_id' => 'required|integer',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        // Call the PythonService to allocate teams
        $response = $this->pythonService->allocateTeams(
            $validated['project_name'],
            $validated['package_id'],
            $validated['start'],
            $validated['end']
        );

        // Return the response to the client, with error handling
        if (isset($response['error'])) {
            return response()->json(['error' => $response['error']], 400); // Return 400 on error
        }

        return response()->json($response);
    }

    /**
     * Get project allocation history.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProjectHistory()
    {
        $response = $this->pythonService->getProjectHistory();

        if (isset($response['error'])) {
            return response()->json(['error' => $response['error']], 400); // Return 400 on error
        }

        return response()->json($response);
    }

    /**
     * Get allocated teams for a specific project.
     *
     * @param string $projectName
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllocatedTeams($projectName)
    {
        $response = $this->pythonService->getAllocatedTeams($projectName);

        if (isset($response['error'])) {
            return response()->json(['error' => $response['error']], 400); // Return 400 on error
        }

        return response()->json($response);
    }


    //hmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm
    public function classifyTask(Request $request)
    {

    }

    public function exportPdf($id)
{
    $project = Project::with('user', 'teams')->findOrFail($id);

    $pdf = Pdf::loadView('pdf.project', compact('project'));

    return $pdf->download("Project_{$project->id}.pdf");
}
}
