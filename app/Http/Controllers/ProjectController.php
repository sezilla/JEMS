<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PythonService;

class ProjectController extends Controller
{
    protected PythonService $pythonService;

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

        if (isset($response['error'])) {
            return response()->json(['error' => $response['error']], 400);
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
            return response()->json(['error' => $response['error']], 400);
        }

        return response()->json($response);
    }

    /**
     * Get allocated teams for a specific project.
     *
     * @param string $projectName
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllocatedTeams(string $projectName)
    {
        $response = $this->pythonService->getAllocatedTeams($projectName);

        if (isset($response['error'])) {
            return response()->json(['error' => $response['error']], 404);
        }

        return response()->json($response);
    }
}
