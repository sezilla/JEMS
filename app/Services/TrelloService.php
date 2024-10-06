<?php

namespace App\Services;

use GuzzleHttp\Client;

class TrelloService
{
    protected $client;
    protected $key;
    protected $token;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.trello.com/1/'
        ]);

        $this->key = env('TRELLO_API_KEY');
        $this->token = env('TRELLO_API_TOKEN');
    }

    private function getAuthParams()
    {
        return [
            'key' => $this->key,
            'token' => $this->token,
        ];
    }

    public function createBoard($name, $desc = '')
    {
        $response = $this->client->request('POST', 'boards/', [
            'query' => array_merge($this->getAuthParams(), [
                'name' => $name,
                'desc' => $desc,
                'defaultLists' => false,
            ]),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function createList($boardId, $name)
    {
        $response = $this->client->request('POST', 'lists', [
            'query' => array_merge($this->getAuthParams(), [
                'idBoard' => $boardId,
                'name' => $name,
            ]),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getBoards()
    {
        $response = $this->client->request('GET', 'members/me/boards', [
            'query' => $this->getAuthParams(),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getCards($boardId)
    {
        $response = $this->client->request('GET', "boards/{$boardId}/cards", [
            'query' => $this->getAuthParams(),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function createCard($listId, $name, $desc = '')
    {
        $response = $this->client->request('POST', 'cards', [
            'query' => array_merge($this->getAuthParams(), [
                'idList' => $listId,
                'name' => $name,
                'desc' => $desc,
            ]),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
