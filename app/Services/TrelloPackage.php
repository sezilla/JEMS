<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class TrelloPackage
{
    protected $client;
    protected $key;
    protected $token;
    protected $workspace;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.trello.com/1/'
        ]);
        $this->key = env('TRELLO_API_KEY');
        $this->token = env('TRELLO_API_TOKEN');
        $this->workspace = env('TRELLO_WORKSPACE_ID');
        
        Log::info('Trello API Key:', ['key' => $this->key]);
        Log::info('Trello API Token:', ['token' => $this->token]);
        Log::info('Trello Workspace ID:', ['workspace' => $this->workspace]);
        
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
                    'prefs_permissionLevel' => 'public',
                    'prefs_isTemplate' => true,
                    // 'prefs_backgroundImage' => $image,
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





    //mainly for tasks
    public function getPackageBoardId($package)
    {
        return $package->trello_board_template_id;
    }
    public function getBoardDetails($boardId)
    {
        try {
            Log::info("Fetching Trello board details for board ID: {$boardId}");

            $response = $this->client->get("boards/{$boardId}", [
                'query' => $this->getAuthParams(),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error("Failed to fetch Trello board details: " . $e->getMessage());
            return null;
        }
    }

    public function getListIdByName($boardId, $listName)
    {
        try {
            Log::info("Fetching Trello List ID for '{$listName}' in board: {$boardId}");

            $response = $this->client->get("boards/{$boardId}/lists", [
                'query' => $this->getAuthParams(),
            ]);

            $lists = json_decode($response->getBody()->getContents(), true);

            foreach ($lists as $list) {
                if ($list['name'] === $listName) {
                    return $list['id'];
                }
            }

            Log::warning("List '{$listName}' not found in Trello board: {$boardId}");
            return null;
        } catch (RequestException $e) {
            Log::error("Failed to fetch Trello lists: " . $e->getMessage());
            return null;
        }
    }

    public function getCardsInDepartmentsList($boardId, $cardName)
    {
        $listId = $this->getListIdByName($boardId, 'Departments');
        if (!$listId) {
            return null;
        }

        try {
            Log::info("Getting Trello cards in list ID: {$listId}");

            $response = $this->client->get("lists/{$listId}/cards", [
                'query' => $this->getAuthParams(),
            ]);

            $cards = json_decode($response->getBody()->getContents(), true);
            foreach ($cards as $card) {
                if ($card['name'] === $cardName) {
                    return $card;
                }
            }

            return null;
        } catch (RequestException $e) {
            Log::error("Failed to get Trello cards: " . $e->getMessage());
            return null;
        }
    }

    public function createDepartmentCard($boardId, $name)
    {
        $listId = $this->getListIdByName($boardId, 'Departments');
        if (!$listId) {
            Log::error("Cannot create Trello card. List ID not found.");
            return null;
        }

        try {
            Log::info("Creating Trello card: {$name} in List ID: {$listId}");

            $response = $this->client->post("cards", [
                'query' => $this->getAuthParams(),
                'json' => [
                    'idList' => $listId,
                    'name' => $name,
                    'desc' => 'Auto-generated department card',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error("Failed to create Trello card: " . $e->getMessage());
            return null;
        }
    }



    public function getChecklistByName($cardId, $checklistName)
    {
        try {
            Log::info("Fetching checklists for Trello card: {$cardId}");
    
            $response = $this->client->get("cards/{$cardId}/checklists", [
                'query' => $this->getAuthParams(),
            ]);
    
            $checklists = json_decode($response->getBody()->getContents(), true);
    
            foreach ($checklists as $checklist) {
                if ($checklist['name'] === $checklistName) {
                    return $checklist;
                }
            }
    
            return null;
        } catch (RequestException $e) {
            Log::error("Failed to fetch Trello checklists: " . $e->getMessage());
            return null;
        }
    }    

    public function createChecklist($cardId, $name)
    {
        try {
            Log::info("Creating Trello checklist: {$name} in card ID: {$cardId}");
    
            $response = $this->client->post("checklists", [
                'query' => $this->getAuthParams(),
                'json' => [
                    'idCard' => $cardId,
                    'name' => $name,
                ],
            ]);
    
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error("Failed to create Trello checklist: " . $e->getMessage());
            return null;
        }
    }
    
    public function createChecklistItem($checklistId, $name)
    {
        try {
            Log::info("Adding checklist item: {$name} to checklist ID: {$checklistId}");

            $response = $this->client->post("checklists/{$checklistId}/checkItems", [
                'query' => $this->getAuthParams(),
                'json' => [
                    'name' => $name,
                    'checked' => false,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error("Failed to add checklist item: " . $e->getMessage());
            return null;
        }
    }

    

}
