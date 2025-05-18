<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Package;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\RequestException;

class TrelloTask
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

    public function getActiveBoards()
    {
        $response = $this->client->request('GET', 'members/me/boards', [
            'query' => $this->getAuthParams(),
        ]);

        $boards = json_decode($response->getBody()->getContents(), true);

        return array_filter($boards, function ($board) {
            return !$board['closed'];
        });
    }

    public function getBoardDepartmentsListId($boardId, $listName = 'Departments')
    {
        try {
            $response = $this->client->request('GET', "boards/{$boardId}/lists", [
                'query' => $this->getAuthParams(),
            ]);

            $lists = json_decode($response->getBody()->getContents(), true);

            foreach ($lists as $list) {
                if (strcasecmp($list['name'], $listName) === 0) {
                    return $list['id'];
                }
            }
        } catch (RequestException $e) {
            Log::error('Failed to get board lists: ' . $e->getMessage());
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
                if (strcasecmp($card['name'], $cardName) === 0) {
                    return $card;
                }
            }
        }
        return null;
    }

    // public function get

    public function getCardStatus($listId, $cardName)
    {
        $card = $this->getCardByName($listId, $cardName);
        if ($card && isset($card['closed'])) {
            return $card['closed'] ? 'checked' : 'unchecked';
        }
        return null;
    }

    public function changeCardStatus($listId, $cardName, $newStatus)
    {
        $card = $this->getCardByName($listId, $cardName);
        if (!$card) {
            Log::error("Card '{$cardName}' not found in list {$listId}.");
            return null;
        }

        $closed = (strcasecmp($newStatus, 'checked') === 0) ? true : false;

        try {
            $response = $this->client->put("cards/{$card['id']}", [
                'query' => array_merge($this->getAuthParams(), [
                    'closed' => $closed,
                ]),
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Failed to change card status: ' . $e->getMessage());
        }

        return null;
    }

    public function getCardChecklists($cardId)
    {
        try {
            $response = $this->client->get("cards/{$cardId}/checklists", [
                'query' => $this->getAuthParams(),
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Failed to get checklists for card ' . $cardId . ': ' . $e->getMessage());
            return [];
        }
    }

    public function getChecklistItems($checklistId)
    {
        try {
            $response = $this->client->get("checklists/{$checklistId}/checkItems", [
                'query' => $this->getAuthParams(),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error("Failed to get checklist items for checklist {$checklistId}: " . $e->getMessage());
            return [];
        }
    }

    public function updateChecklistItemState($cardId, $checkItemId, $state)
    {
        if (!in_array($state, ['complete', 'incomplete'])) {
            Log::error("Invalid checklist item state: {$state}");
            return null;
        }

        try {
            $response = $this->client->put("cards/{$cardId}/checkItem/{$checkItemId}", [
                'query' => array_merge($this->getAuthParams(), [
                    'state' => $state,
                ]),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error("Failed to update checklist item {$checkItemId} in card {$cardId} to state {$state}: " . $e->getMessage());
            return null;
        }
    }

    public function setDueDateToCard($listId, $cardName, $dueDate)
    {
        $card = $this->getCardByName($listId, $cardName);
        if (!$card) {
            Log::error("Card '{$cardName}' not found in list {$listId}.");
            return null;
        }

        try {
            $response = $this->client->put("cards/{$card['id']}", [
                'query' => array_merge($this->getAuthParams(), [
                    'due' => $dueDate,
                ]),
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Failed to set due date for card: ' . $e->getMessage());
        }

        return null;
    }

    public function setCheckItemDueDate($cardId, $checkItemId, $dueDate)
    {
        try {
            $response = $this->client->put("cards/{$cardId}/checkItem/{$checkItemId}", [
                'query' => array_merge($this->getAuthParams(), [
                    'due' => $dueDate,
                ]),
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            Log::info("Set due date for checklist item {$checkItemId} on card {$cardId} to {$dueDate}.");

            return $responseData;
        } catch (RequestException $e) {
            Log::error('Failed to set due date for checklist item: ' . $e->getMessage());
            return null;
        }
    }

    public function setCheckItemState($cardId, $checkItemId, $state)
    {
        try {
            $response = $this->client->put("cards/{$cardId}/checkItem/{$checkItemId}", [
                'query' => array_merge($this->getAuthParams(), [
                    'state' => in_array($state, ['complete', 'incomplete']) ? $state : 'incomplete',
                ]),
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            Log::info("Set state for checklist item {$checkItemId} on card {$cardId} to {$state}.");

            return $responseData;
        } catch (RequestException $e) {
            Log::error('Failed to set due date for checklist item: ' . $e->getMessage());
            return null;
        }
    }

    public function updateCheckItemDetails($cardId, $checkItemId, $name = null, $due = null, $state = null)
    {
        try {
            $queryParams = $this->getAuthParams();

            if ($name !== null) {
                $queryParams['name'] = $name;
            }

            if ($due !== null) {
                $queryParams['due'] = $due;
            }

            if ($state !== null && in_array($state, ['complete', 'incomplete'])) {
                $queryParams['state'] = $state;
            }

            $response = $this->client->put("cards/{$cardId}/checkItem/{$checkItemId}", [
                'query' => $queryParams,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            Log::info("Updated checklist item {$checkItemId} on card {$cardId} with details: name='{$name}', due='{$due}', state='{$state}'.");

            return $responseData;
        } catch (RequestException $e) {
            Log::error("Failed to update checklist item {$checkItemId} on card {$cardId}: " . $e->getMessage());
            return null;
        }
    }

    public function createCheckItem($checklistId, $name, $due)
    {
        try {
            $response = $this->client->post("checklists/{$checklistId}/checkItems", [
                'query' => array_merge($this->getAuthParams(), [
                    'name' => $name,
                    'due' => $due,
                ]),
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            Log::info("Created checklist item for {$checklistId} with name '{$name}'.");

            return $responseData;
        } catch (RequestException $e) {
            Log::error('Failed to set due date for checklist item: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteCheckItem($checklistId, $checkItemId)
    {
        try {
            $response = $this->client->delete("checklists/{$checklistId}/checkItems/{$checkItemId}", [
                'query' => $this->getAuthParams(),
            ]);

            Log::info("Deleted checklist item {$checkItemId} from checklist {$checklistId}.");

            // Return true to indicate success, since Trello returns no content on success
            return true;
        } catch (RequestException $e) {
            Log::error('Failed to delete checklist item: ' . $e->getMessage());
            return false;
        }
    }

    public function setCardDue($cardId, $dueDate)
    {
        try {
            $response = $this->client->put("cards/{$cardId}", [
                'query' => array_merge($this->getAuthParams(), [
                    'due' => $dueDate,
                ]),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Failed to set due date for card', [
                'card_id'  => $cardId,
                'due_date' => $dueDate,
                'message'  => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function updateCard($cardId, $cardData)
    {
        try {
            $response = $this->client->put("cards/{$cardId}", [
                'query' => array_merge($this->getAuthParams(), $cardData),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Failed to update card', [
                'card_id' => $cardId,
                'card_data' => $cardData,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function completeCheckItemStatus($cardId, $checkItemId)
    {
        try {
            $response = $this->client->put("cards/{$cardId}/checkItem/{$checkItemId}", [
                'query' => array_merge($this->getAuthParams(), [
                    'state' => 'complete',
                ]),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Failed to update check item status: ' . $e->getMessage());
            return null;
        }
    }

    public function getCard($cardId)
    {
        $response = $this->client->get("cards/{$cardId}", [
            'query' => $this->getAuthParams(),
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getChecklist($cardId)
    {
        $response = $this->client->get("cards/{$cardId}/checklists", [
            'query' => $this->getAuthParams(),
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function createChecklist($cardId, $checklistName)
    {
        $response = $this->client->post("cards/{$cardId}/checklists", [
            'query' => array_merge($this->getAuthParams(), [
                'name' => $checklistName,
            ]),
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function deleteCheckItemByCardId($cardId, $checkItemId)
    {
        $response = $this->client->delete("cards/{$cardId}/checkItem/{$checkItemId}", [
            'query' => $this->getAuthParams(),
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}
