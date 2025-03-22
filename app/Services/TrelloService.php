<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Package;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;

class TrelloService
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

        // Log::info('Trello API Key:', ['key' => $this->key]);                //uncomment for testing
        // Log::info('Trello API Token:', ['token' => $this->token]);
        // Log::info('Trello Workspace ID:', ['workspace' => $this->workspace]);

        Log::info('Loaded Trello configuration.');
    }

    private function getAuthParams()
    {
        return [
            'key' => $this->key,
            'token' => $this->token,
        ];
    }

    public function createBoardFromTemplate($name, $packageName)
    {
        try {
            Log::info('Creating Trello board with name: ' . $name);

            // Determine the template board ID based on package name
            $templateBoardId = $this->templateBoardIds[$packageName] ?? null;

            if (!$templateBoardId) {
                Log::error('Invalid package name: ' . $packageName);
                return null;
            }

            $response = $this->client->post("boards/", [
                'query' => array_merge($this->getAuthParams(), [
                    'name' => $name,
                    'idBoardSource' => $templateBoardId,
                    'idOrganization' => $this->workspace,
                    'prefs_permissionLevel' => 'public'
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

    //for testing boards kinemerlu
    public function getBoards()
    {
        $response = $this->client->request('GET', 'members/me/boards', [
            'query' => $this->getAuthParams(),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }


    //for date and etc... kinemerlu
    public function getBoardLists($boardId)
    {
        try {
            $response = $this->client->get("boards/{$boardId}/lists", [
                'query' => $this->getAuthParams(),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Failed to get Trello board lists: ' . $e->getMessage());
            return null;
        }
    }
    public function getBoardListByName($boardId, $listName)
    {
        $lists = $this->getBoardLists($boardId);
        if ($lists) {
            foreach ($lists as $list) {
                if ($list['name'] === $listName) {
                    return $list;
                }
            }
        }
        return null;
    }

    public function getListCards($listId)
    {
        try {
            $response = $this->client->get("lists/{$listId}/cards", [
                'query' => $this->getAuthParams(),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Failed to get Trello list cards: ' . $e->getMessage());
            return null;
        }
    }

    public function getCardByName($listId, $cardName)
    {
        $cards = $this->getListCards($listId);
        if ($cards) {
            foreach ($cards as $card) {
                if ($card['name'] === $cardName) {
                    return $card;
                }
            }
        }
        return null;
    }
    public function getCardIdByName($listId, $cardName)
    {
        $cards = $this->getListCards($listId);
        if ($cards) {
            foreach ($cards as $card) {
                if ($card['name'] === $cardName) {
                    return $card['id'];
                }
            }
        }
        return null;
    }

    public function getCardData($cardId)
    {
        try {
            Log::info('Fetching data for Trello card with ID: ' . $cardId);
            $response = $this->client->get("cards/{$cardId}", [
                'query' => $this->getAuthParams(),
            ]);

            $responseBody = $response->getBody()->getContents();
            Log::info('Trello API Response: ' . $responseBody);

            return json_decode($responseBody, true);
        } catch (RequestException $e) {
            Log::error('Failed to retrieve data for Trello card: ' . $e->getMessage());
            if ($e->hasResponse()) {
                $responseContent = $e->getResponse()->getBody()->getContents();
                Log::error('Response: ' . $responseContent);
                Log::error('Response Status Code: ' . $e->getResponse()->getStatusCode());
            }
            return null;
        }
    }



    public function updateCard($cardId, $data)
    {
        try {
            $response = $this->client->put("cards/{$cardId}", [
                'query' => $this->getAuthParams(),
                'json' => $data, // Using 'json' to pass data as JSON, including the due field
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Failed to update Trello card: ' . $e->getMessage());
            return null;
        }
    }

    public function createCardInList($listId, $cardName)
    {
        try {
            $response = $this->client->post("lists/{$listId}/cards", [
                'query' => $this->getAuthParams(),
                'form_params' => [
                    'name' => $cardName,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Failed to create Trello card: ' . $e->getMessage());
            return null;
        }
    }


    public function fetchTrelloCards($boardId, $listName = 'Departments')
    {
        $list = $this->getBoardListByName($boardId, $listName);

        if ($list) {
            // Retrieve each card in the list along with its checklists
            $cards = $this->getListCardsWithChecklists($list['id']);
            return $cards;
        }

        Log::warning("List '{$listName}' not found on board '{$boardId}'.");
        return null;
    }

    public function getListCardsWithChecklists($listId)
    {
        try {
            // Get all cards in the specified list
            $response = $this->client->get("lists/{$listId}/cards", [
                'query' => $this->getAuthParams(),
            ]);
            $cards = json_decode($response->getBody()->getContents(), true);

            // Fetch checklists for each card and add them to the card array
            foreach ($cards as &$card) {
                $card['checklist'] = $this->getCardChecklists($card['id']);
            }

            return $cards;
        } catch (RequestException $e) {
            Log::error('Failed to get Trello list cards: ' . $e->getMessage());
            return null;
        }
    }

    public function getCardChecklists($cardId)
    {
        try {
            $response = $this->client->get("cards/{$cardId}/checklists", [
                'query' => $this->getAuthParams(),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Failed to get checklists for Trello card: ' . $e->getMessage());
            return null;
        }
    }

    public function addAttachmentToCard($cardId, $filePath)
    {
        $url = "https://api.trello.com/1/cards/{$cardId}/attachments";
        $fileFullPath = storage_path("app/public/{$filePath}");

        // Ensure the file exists before proceeding
        if (!file_exists($fileFullPath)) {
            throw new \Exception("File not found at path: {$fileFullPath}");
        }

        $params = [
            'key' => env('TRELLO_API_KEY'),
            'token' => env('TRELLO_API_TOKEN'),
        ];

        $response = $this->client->request('POST', $url, [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($fileFullPath, 'r'),
                    'filename' => basename($fileFullPath) // Ensure the filename is passed correctly
                ],
                [
                    'name' => 'key',
                    'contents' => $params['key'],
                ],
                [
                    'name' => 'token',
                    'contents' => $params['token'],
                ],
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
