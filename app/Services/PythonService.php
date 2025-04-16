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

    public function allocateUserToTask(int $projectId, array $dataArray, array $usersArray): array
    {
        $url = "{$this->baseUrl}/allocate-user-to-task";

        try {
            $response = Http::timeout(360)->post($url, [
                'project_id' => $projectId,
                'data_array' => $dataArray['data_array'],
                'users' => $usersArray,
            ]);

            if ($response->failed()) {
                Log::error('Python API error response: ' . $response->body());
                return [
                    'success' => false,
                    'error' => 'Failed to connect to Python API',
                    'http_status' => $response->status()
                ];
            }

            $responseData = $response->json();

            Log::info('Full Python API Response', [
                'raw_response' => $response->body(),
                'parsed_response' => $responseData,
            ]);

            if (!isset($responseData['success'])) {
                $responseData['success'] = true;
            }

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Exception during Python API call: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
