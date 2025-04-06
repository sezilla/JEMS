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

    public function allocateTeams(int $projectId, int $packageId, string $startDate, string $endDate): array
    {
        $url = "{$this->baseUrl}/allocate-teams";

        try {
            $startDate = date('Y-m-d', strtotime($startDate));
            $endDate = date('Y-m-d', strtotime($endDate));

            $request = Http::post($url, [
                'project_id' => $projectId,
                'package_id' => $packageId,
                'start' => $startDate,
                'end' => $endDate,
            ]);


            if ($request->successful()) {
                $data = $request->json();

                Log::info('PythonService::allocateTeams Raw Response', ['response' => $data]);

                if (isset($data['success']) && $data['success']) {
                    return $data['allocated_teams'] ?? [];
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

    public function special_request(int $projectId, string $specialRequest): array
    {
        $url = "{$this->baseUrl}/special-request";

        try {
            $response = Http::post($url, [
                'project_id' => $projectId,
                'special_request' => $specialRequest,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PythonService::special_request Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['error' => 'Failed to classify task. Please try again.'];
        } catch (\Exception $e) {
            Log::error('PythonService::special_request Exception', [
                'message' => $e->getMessage(),
            ]);

            return ['error' => 'An error occurred while classifying tasks. Please check the logs.'];
        }
    }

    public function predictCategories(int $projectId): array
    {
        $url = "{$this->baseUrl}/generate-schedule";

        try {
            $response = Http::timeout(360)
                ->post($url, [
                    'project_id' => $projectId,
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
