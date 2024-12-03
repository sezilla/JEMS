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



// <?php

// namespace App\Services;

// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Log;

// class PythonService
// {
//     protected $baseUrl;

//     public function __construct()
//     {
//         // Set the base URL for your Python FastAPI service
//         $this->baseUrl = env('PYTHON_SERVICE_URL');
//     }

//     /**
//      * Allocate teams for a project.
//      *
//      * @param string $projectName
//      * @param int $packageId
//      * @param string $startDate
//      * @param string $endDate
//      * @return array
//      */
//     public function allocateTeams(string $projectName, int $packageId, string $startDate, string $endDate): array
//     {
//         $url = "{$this->baseUrl}/allocate-teams";

//         try {
//             $response = Http::post($url, [
//                 'project_name' => $projectName,
//                 'package_id' => $packageId,
//                 'start' => $startDate,
//                 'end' => $endDate,
//             ]);

//             if ($response->successful()) {
//                 return $response->json();
//             }

//             Log::error('PythonService::allocateTeams Error', [
//                 'status' => $response->status(),
//                 'body' => $response->body(),
//             ]);

//             return [
//                 'error' => 'Failed to allocate teams. Please try again.',
//             ];
//         } catch (\Exception $e) {
//             Log::error('PythonService::allocateTeams Exception', [
//                 'message' => $e->getMessage(),
//             ]);

//             return [
//                 'error' => 'An error occurred while allocating teams. Please check the logs.',
//             ];
//         }
//     }
//     /**
//      * Retrieve project history from the Python service.
//      *
//      * @return array
//      */
//     public function getProjectHistory(): array
//     {
//         $url = $this->baseUrl . '/project-history';

//         try {
//             $response = Http::get($url);

//             if ($response->successful()) {
//                 return $response->json();
//             }

//             Log::error('PythonService::getProjectHistory Error', [
//                 'status' => $response->status(),
//                 'body' => $response->body(),
//             ]);

//             return [
//                 'error' => 'Failed to fetch project history. Please try again.',
//             ];
//         } catch (\Exception $e) {
//             Log::error('PythonService::getProjectHistory Exception', [
//                 'message' => $e->getMessage(),
//             ]);

//             return [
//                 'error' => 'An error occurred while fetching project history. Please check the logs.',
//             ];
//         }
//     }

//     /**
//      * Retrieve allocated teams for a specific project from the Python service.
//      *
//      * @param string $projectName
//      * @return array
//      */
//     public function getAllocatedTeams(string $projectName): array
//     {
//         $url = "{$this->baseUrl}/allocated-teams/{$projectName}";

//         try {
//             $response = Http::get($url);

//             if ($response->successful()) {
//                 return $response->json();
//             }

//             Log::error('PythonService::getAllocatedTeams Error', [
//                 'status' => $response->status(),
//                 'body' => $response->body(),
//             ]);

//             return [
//                 'error' => 'Failed to fetch allocated teams. Please try again.',
//             ];
//         } catch (\Exception $e) {
//             Log::error('PythonService::getAllocatedTeams Exception', [
//                 'message' => $e->getMessage(),
//             ]);

//             return [
//                 'error' => 'An error occurred while fetching allocated teams. Please check the logs.',
//             ];
//         }
//     }

// }
