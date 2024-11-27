<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PythonService;

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
        $validated = $request->validate([
            'project_name' => 'required|string',
            'package_id' => 'required|integer',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $response = $this->pythonService->allocateTeams(
            $validated['project_name'],
            $validated['package_id'],
            $validated['start'],
            $validated['end']
        );

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
        return response()->json($response);
    }
}
