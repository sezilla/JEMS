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
}
