<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TeamAllocatorController extends Controller
{
    private $serviceUrl;

    public function __construct()
    {
        $this->serviceUrl = env('TEAM_ALLOCATOR_SERVICE', 'http://localhost:8000');
    }

    public function allocateTeams(Request $request)
    {
        $response = Http::post("{$this->serviceUrl}/allocate-teams", [
            'project_name' => $request->input('project_name'),
            'package_id' => $request->input('package_id'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Allocation failed', 'details' => $response->json()], 400);
        }

        return response()->json($response->json());
    }

    public function getProjectHistory()
    {
        $response = Http::get("{$this->serviceUrl}/project-history");

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch history'], 400);
        }

        return response()->json($response->json());
    }
}
