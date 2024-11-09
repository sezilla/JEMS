<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use App\Models\Package;

class TrelloPackage
{
    protected $client;
    protected $key;
    protected $token;
    protected $workspace;
    protected $templateBoardIds;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.trello.com/1/'
        ]);
        $this->key = env('TRELLO_API_KEY');
        $this->token = env('TRELLO_API_TOKEN');
        $this->workspace = env('TRELLO_WORKSPACE_ID');
        $this->templateBoardIds = Package::pluck('trello_board_template_id', 'name')->filter()->toArray();
        
        Log::info('Loaded Trello configuration.');
    }

    private function getAuthParams()
    {
        return [
            'key' => $this->key,
            'token' => $this->token,
        ];
    }

    public function createPackageBoard($name)
    {
        try {
            Log::info('Creating Trello board with name: ' . $name);

            $response = $this->client->post("boards/", [
                'query' => array_merge($this->getAuthParams(), [
                    'name' => $name,
                    'idOrganization' => $this->workspace,
                    'prefs_permissionLevel' => 'private',
                    'prefs_isTemplate' => true
                ]),
            ]);

            $responseBody = $response->getBody()->getContents();
            Log::info('Trello API Response: ' . $responseBody);

            return json_decode($responseBody, true);
        } catch (RequestException $e) {
            Log::error('Failed to create Trello board: ' . $e->getMessage());
            if ($e->hasResponse()) {
                $responseContent = $e->getResponse()->getBody()->getContents();
                Log::error('Response: ' . $responseContent);
                Log::error('Response Status Code: ' . $e->getResponse()->getStatusCode());
            }
            return null;
        }
    }

    public function createList($boardId, $name)
    {
        try {
            Log::info('Creating Trello list with name: ' . $name . ' on board ID: ' . $boardId);
            $response = $this->client->post('lists', [
                'query' => array_merge($this->getAuthParams(), [
                    'idBoard' => $boardId,
                    'name' => $name,
                ]),
            ]);

            $responseBody = $response->getBody()->getContents();
            Log::info('Trello API Response: ' . $responseBody);

            return json_decode($responseBody, true);
        } catch (RequestException $e) {
            Log::error('Failed to create Trello list: ' . $e->getMessage());
            if ($e->hasResponse()) {
                $responseContent = $e->getResponse()->getBody()->getContents();
                Log::error('Response: ' . $responseContent);
                Log::error('Response Status Code: ' . $e->getResponse()->getStatusCode());
            }
            return null;
        }
    }

    public function createCard($listId, $name, $description = '')
    {
        try {
            Log::info('Creating Trello card with name: ' . $name . ' in list ID: ' . $listId);
            $response = $this->client->post('cards', [
                'query' => $this->getAuthParams(),
                'json' => [
                    'idList' => $listId,
                    'name' => $name,
                    'desc' => $description,
                ],
            ]);

            $responseBody = $response->getBody()->getContents();
            Log::info('Trello API Response: ' . $responseBody);

            return json_decode($responseBody, true);
        } catch (RequestException $e) {
            Log::error('Failed to create Trello card: ' . $e->getMessage());
            if ($e->hasResponse()) {
                $responseContent = $e->getResponse()->getBody()->getContents();
                Log::error('Response: ' . $responseContent);
                Log::error('Response Status Code: ' . $e->getResponse()->getStatusCode());
            }
            return null;
        }
    }
}
