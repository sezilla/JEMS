<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('PYTHON_SERVICE_URL', 'http://127.0.0.1:3000');
    }


    /**
     * Allocate teams for a project.
     *
     * @param string $projectId
     * @param int $packageId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function allocateTeams(string $projectId, int $packageId, string $startDate, string $endDate): array
    {
        $url = "{$this->baseUrl}/allocate-teams";

        try {
            $startDate = date('Y-m-d', strtotime($startDate));
            $endDate = date('Y-m-d', strtotime($endDate));

            $response = Http::post($url, [
                'project_id' => $projectId,
                'package_id' => $packageId,
                'start' => $startDate,
                'end' => $endDate,
            ]);


            if ($response->successful()) {
                $data = $response->json();

                Log::info('PythonService::allocateTeams Raw Response', ['response' => $data]); // 🔥 Debugging

                if (isset($data['success']) && $data['success']) {
                    return $data['allocated_teams'] ?? [];  // ✅ Fix: Directly return allocated_teams
                }
                return ['error' => 'Failed to allocate teams. Response format is incorrect.'];
            }

            return ['error' => 'Failed to allocate teams. Please try again.'];
        } catch (\Exception $e) {
            Log::error('PythonService::allocateTeams Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ['error' => 'An error occurred while allocating teams. Please check the logs.'];
        }
    }

    /**
     * Classify tasks based on special request.
     *
     * @param string $specialRequest
     * @return array
     */
    public function classifyTask(string $specialRequest): array
    {
        $url = "{$this->baseUrl}/classify-task";

        try {
            $response = Http::post($url, [
                'task' => $specialRequest, // 🔥 Fix: Change 'special_request' to 'task'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PythonService::classifyTask Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['error' => 'Failed to classify task. Please try again.'];
        } catch (\Exception $e) {
            Log::error('PythonService::classifyTask Exception', [
                'message' => $e->getMessage(),
            ]);

            return ['error' => 'An error occurred while classifying tasks. Please check the logs.'];
        }
    }

    /**
     * Predict categories for a project.
     *
     * @param string $projectName
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function predictCategories(string $projectName, string $startDate, string $endDate): array
    {
        $url = "{$this->baseUrl}/predict_categories";

        try {
            $response = Http::post($url, [
                'project_name' => $projectName,
                'start' => $startDate,
                'end' => $endDate,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PythonService::predictCategories Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['error' => 'Failed to predict categories. Please try again.'];
        } catch (\Exception $e) {
            Log::error('PythonService::predictCategories Exception', [
                'message' => $e->getMessage(),
            ]);

            return ['error' => 'An error occurred while predicting categories. Please check the logs.'];
        }
    }
}
