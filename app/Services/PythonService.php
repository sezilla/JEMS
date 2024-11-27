<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonService
{
    protected $baseUrl;

    public function __construct()
    {
        // Set the base URL for your Python FastAPI service
        $this->baseUrl = env('PYTHON_SERVICE_URL', 'http://127.0.0.1:8000'); // Update to your Python API URL
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
        $url = "{$this->baseUrl}/allocate-teams";

        try {
            $response = Http::post($url, [
                'project_name' => $projectName,
                'package_id' => $packageId,
                'start' => $startDate,
                'end' => $endDate,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PythonService::allocateTeams Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'error' => 'Failed to allocate teams. Please try again.',
            ];
        } catch (\Exception $e) {
            Log::error('PythonService::allocateTeams Exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'error' => 'An error occurred while allocating teams. Please check the logs.',
            ];
        }
    }
    /**
     * Retrieve project history from the Python service.
     *
     * @return array
     */
    public function getProjectHistory(): array
    {
        $url = $this->baseUrl . '/project-history';

        try {
            $response = Http::get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PythonService::getProjectHistory Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'error' => 'Failed to fetch project history. Please try again.',
            ];
        } catch (\Exception $e) {
            Log::error('PythonService::getProjectHistory Exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'error' => 'An error occurred while fetching project history. Please check the logs.',
            ];
        }
    }
}
