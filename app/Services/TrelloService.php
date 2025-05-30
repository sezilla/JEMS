<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Package;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\Traits\TrelloRateLimiter;
use GuzzleHttp\Exception\RequestException;

class TrelloService
{
    use TrelloRateLimiter;

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

    public function createBoardFromTemplate($name, $packageName)
    {
        return $this->makeRateLimitedRequest(function () use ($name, $packageName) {
            try {
                Log::info('Creating Trello board with name: ' . $name);

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
        });
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

    public function createChecklist($cardId, $name)
    {
        return $this->makeRateLimitedRequest(function () use ($cardId, $name) {
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
        });
    }

    public function createChecklistItem($checklistId, $name)
    {
        return $this->makeRateLimitedRequest(function () use ($checklistId, $name) {
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
        });
    }

    public function getBoards()
    {
        $response = $this->client->request('GET', 'members/me/boards', [
            'query' => $this->getAuthParams(),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getBoardLists($boardId)
    {
        return $this->makeRateLimitedRequest(function () use ($boardId) {
            try {
                $response = $this->client->get("boards/{$boardId}/lists", [
                    'query' => $this->getAuthParams(),
                ]);

                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error('Failed to get Trello board lists: ' . $e->getMessage());
                return null;
            }
        });
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
        return $this->makeRateLimitedRequest(function () use ($listId) {
            try {
                $response = $this->client->get("lists/{$listId}/cards", [
                    'query' => $this->getAuthParams(),
                ]);

                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error('Failed to get Trello list cards: ' . $e->getMessage());
                return null;
            }
        });
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

    public function getCardsNameAndId($listId)
    {
        try {
            $cards = $this->getListCards($listId);
            if ($cards) {
                return array_map(function ($card) {
                    return [
                        'name' => $card['name'],
                        'id' => $card['id'],
                    ];
                }, $cards);
            }
            return [];
        } catch (RequestException $e) {
            Log::error('Failed to get card names and IDs for list ID: ' . $listId . '. Error: ' . $e->getMessage());
            return [];
        }
    }

    public function setChecklistItemDueDate($cardId, $checklistItemId, $dueDate)
    {
        return $this->makeRateLimitedRequest(function () use ($cardId, $checklistItemId, $dueDate) {
            try {
                $isoDueDate = \Carbon\Carbon::parse($dueDate)->toIso8601String();

                $response = $this->client->put("cards/{$cardId}/checkItem/{$checklistItemId}", [
                    'query' => $this->getAuthParams(),
                    'json'  => [
                        'due' => $isoDueDate,
                    ],
                ]);

                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error('Failed to set checklist item due date: ' . $e->getMessage(), [
                    'card_id'         => $cardId,
                    'checklist_item_id' => $checklistItemId,
                    'due_date'        => $dueDate,
                ]);
                return null;
            }
        });
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

    public function getCardsByListId($listId)
    {
        try {
            Log::info("Fetching Trello cards for list ID: {$listId}");

            $response = $this->client->get("lists/{$listId}/cards", [
                'query' => $this->getAuthParams(),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error("Failed to fetch Trello cards: " . $e->getMessage());
            return [];
        }
    }

    public function getChecklistsByCardId($cardId)
    {
        return $this->makeRateLimitedRequest(function () use ($cardId) {
            try {
                Log::info("Fetching checklists for card ID: {$cardId}");

                $response = $this->client->get("cards/{$cardId}/checklists", [
                    'query' => $this->getAuthParams(),
                ]);

                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error("Failed to fetch checklists: " . $e->getMessage());
                return [];
            }
        });
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

    public function getChecklistItems($checklistId)
    {
        return $this->makeRateLimitedRequest(function () use ($checklistId) {
            try {
                $response = $this->client->get("checklists/{$checklistId}/checkItems", [
                    'query' => $this->getAuthParams(),
                ]);

                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error("Failed to get checklist items for checklist {$checklistId}: " . $e->getMessage());
                return [];
            }
        });
    }

    public function updateCard($cardId, $data)
    {
        return $this->makeRateLimitedRequest(function () use ($cardId, $data) {
            try {
                $response = $this->client->put("cards/{$cardId}", [
                    'query' => $this->getAuthParams(),
                    'json' => $data,
                ]);

                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error('Failed to update Trello card: ' . $e->getMessage());
                return null;
            }
        });
    }

    public function createCardInList($listId, $cardName)
    {
        return $this->makeRateLimitedRequest(function () use ($listId, $cardName) {
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
        });
    }

    public function fetchTrelloCards($boardId, $listName = 'Departments')
    {
        $list = $this->getBoardListByName($boardId, $listName);

        if ($list) {
            $cards = $this->getListCardsWithChecklists($list['id']);
            return $cards;
        }

        Log::warning("List '{$listName}' not found on board '{$boardId}'.");
        return null;
    }

    public function getListCardsWithChecklists($listId)
    {
        try {
            $response = $this->client->get("lists/{$listId}/cards", [
                'query' => $this->getAuthParams(),
            ]);
            $cards = json_decode($response->getBody()->getContents(), true);

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
        return $this->makeRateLimitedRequest(function () use ($cardId, $filePath) {
            $url = "https://api.trello.com/1/cards/{$cardId}/attachments";
            $fileFullPath = storage_path("app/public/{$filePath}");

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
                        'filename' => basename($fileFullPath)
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
        });
    }

    public function getDepartmentsListId($boardId)
    {
        $lists = $this->getBoardLists($boardId);
        if ($lists) {
            foreach ($lists as $list) {
                if ($list['name'] === 'Departments') {
                    return $list['id'];
                }
            }
        }
        return null;
    }

    public function closeBoard($boardId)
    {
        return $this->makeRateLimitedRequest(function () use ($boardId) {
            try {
                $response = $this->client->put("boards/{$boardId}", [
                    'query' => array_merge($this->getAuthParams(), [
                        'closed' => 'true',
                    ]),
                ]);

                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error('Failed to close Trello board: ' . $e->getMessage());
                return null;
            }
        });
    }

    public function getCheckItemCount($boardId)
    {
        $listId = $this->getDepartmentsListId($boardId);

        if (!$listId) {
            Log::error("Departments list not found on board ID: {$boardId}");
            return 0;
        }

        $cards = $this->getListCards($listId);

        if (!$cards) {
            Log::error("No cards found in Departments list on board ID: {$boardId}");
            return 0;
        }

        $totalCheckItems = 0;

        foreach ($cards as $card) {
            try {
                $response = $this->client->get("cards/{$card['id']}/checkItemStates", [
                    'query' => $this->getAuthParams(),
                ]);

                $checkItemStates = json_decode($response->getBody()->getContents(), true);
                $totalCheckItems += count($checkItemStates);
            } catch (RequestException $e) {
                Log::error("Failed to fetch check item states for card ID: {$card['id']}. Error: " . $e->getMessage());
            }
        }

        return $totalCheckItems;
    }

    public function getTrelloBoardProgress($boardId)
    {
        try {
            Log::info('Starting to fetch Trello board progress', ['board_id' => $boardId]);

            $cards = $this->fetchTrelloCards($boardId);
            if (!$cards) {
                Log::error('No cards found for board', ['board_id' => $boardId]);
                return [];
            }

            Log::info('Fetched Trello cards', [
                'board_id' => $boardId,
                'card_count' => count($cards),
                'cards' => array_map(function ($card) {
                    return [
                        'id' => $card['id'],
                        'name' => $card['name']
                    ];
                }, $cards)
            ]);

            $progress = [];

            foreach ($cards as $card) {
                $cardId = $card['id'];
                $cardName = $card['name'];

                Log::info('Processing card', [
                    'card_id' => $cardId,
                    'card_name' => $cardName
                ]);

                $checklists = $this->getCardChecklists($cardId);
                if (!$checklists) {
                    Log::warning('No checklists found for card', [
                        'card_id' => $cardId,
                        'card_name' => $cardName
                    ]);
                    continue;
                }

                $totalItems = 0;
                $completedItems = 0;

                foreach ($checklists as $checklist) {
                    Log::info('Processing checklist', [
                        'checklist_id' => $checklist['id'],
                        'checklist_name' => $checklist['name'],
                        'item_count' => count($checklist['checkItems'] ?? [])
                    ]);

                    foreach ($checklist['checkItems'] as $item) {
                        $totalItems++;
                        if ($item['state'] === 'complete') {
                            $completedItems++;
                        }
                    }
                }

                $percentage = $totalItems > 0 ? intval(($completedItems / $totalItems) * 100) : 0;

                Log::info('Card progress calculated', [
                    'card_name' => $cardName,
                    'total_items' => $totalItems,
                    'completed_items' => $completedItems,
                    'percentage' => $percentage
                ]);

                $progress[$cardName] = $percentage;
            }

            Log::info('Board progress calculation completed', [
                'board_id' => $boardId,
                'progress' => $progress
            ]);

            return $progress;
        } catch (RequestException $e) {
            Log::error('Failed to get board progress', [
                'board_id' => $boardId,
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);
            return [];
        } catch (\Exception $e) {
            Log::error('Unexpected error getting board progress', [
                'board_id' => $boardId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    public function updateBoard($boardId, array $data)
    {
        return $this->makeRateLimitedRequest(function () use ($boardId, $data) {
            $response = $this->client->put("boards/{$boardId}", [
                'query' => $this->getAuthParams(),
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        });
    }
}
