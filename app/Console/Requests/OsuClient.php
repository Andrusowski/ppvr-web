<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Console\Requests;

use GuzzleHttp\Client;

class OsuClient
{
    /**
     * @param string $playerName
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getUser(string $playerName)
    {
        $response = $this->createClient()->request('GET', '/api/get_user', [
            'query' => [
                'k' => env('OSU_API_KEY'),
                'u' => $playerName,
                'type' => 'string',
            ],
        ])->getBody()->getContents();

        return json_decode($response, false, 512, JSON_THROW_ON_ERROR);
    }

    public function getPlayerPage(string $playerId): string
    {
        $client = new Client(['base_url' => 'https://osu.ppy.sh']);

        return $client->request('GET', '/users/' . $playerId)
            ->getBody()
            ->getContents();
    }

    /**
     * @return Client
     */
    private function createClient(): Client
    {
        return new Client(['base_uri' => 'https://osu.ppy.sh']);
    }
}
