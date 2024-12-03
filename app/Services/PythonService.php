<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('PYTHON_SERVICE_URL', 'http://localhost:8000'), '/');
    }

    /**
     * Allocate teams for a project.
     *
     * @param string $projectName
     * @param int $packageId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function allocateTeams(string $projectName, int $packageId, string $startDate, string $endDate): array
    {
        return $this->sendRequest('POST', '/allocate-teams', [
            'project_name' => $projectName,
            'package_id' => $packageId,
            'start' => $startDate,
            'end' => $endDate,
        ]);
    }

    /**
     * Retrieve project allocation history.
     *
     * @return array
     */
    public function getProjectHistory(): array
    {
        return $this->sendRequest('GET', '/project-history');
    }

    /**
     * Retrieve allocated teams for a specific project.
     *
     * @param string $projectName
     * @return array
     */
    public function getAllocatedTeams(string $projectName): array
    {
        return $this->sendRequest('GET', "/allocated-teams/{$projectName}");
    }

    /**
     * Send a request to the Python FastAPI service.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    protected function sendRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = "{$this->baseUrl}{$endpoint}";

        try {
            $response = Http::timeout(10)->{$method}($url, $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("PythonService::sendRequest Error", [
                'method' => $method,
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['error' => 'Failed to communicate with the Python service.'];
        } catch (\Exception $e) {
            Log::error("PythonService::sendRequest Exception", [
                'method' => $method,
                'url' => $url,
                'message' => $e->getMessage(),
            ]);

            return ['error' => 'An unexpected error occurred.'];
        }
    }
}
