<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Services\Clients;

use App\Models\Api\User;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class OsuClient
{
    /**
     * @var DateTime
     */
    private static $lastRequest;

    /**
     * @var string
     */
    private static $accessToken;

    public function getUser(string $playerName): User
    {
        try {
            $response = $this->createClient()->get("/api/v2/users/{$playerName}/osu", [
                'headers' => [
                    'Authorization' => "Bearer {$this->getAccessToken()}",
                ],
            ]);
        } catch (ClientException $exception) {
            return new User();
        }

        $userData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return new User($userData);
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
        if (static::$lastRequest === null) {
            static::$lastRequest = new DateTime();
        }

        //take a break to prevent osu!api spam
        while (static::$lastRequest->diff(new DateTime())->s < 1) {
            usleep(200000);
        }
        self::$lastRequest = new DateTime();

        return new Client(['base_uri' => 'https://osu.ppy.sh']);
    }

    private function getAccessToken(): string
    {
        if (static::$accessToken === null) {
            $response = $this->createClient()->post('/oauth/token', [
                'json' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => env('OSU_CLIENT_ID'),
                    'client_secret' => env('OSU_CLIENT_SECRET'),
                    'scope' => 'public',
                ],
            ]);

            $parsedResponse = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            static::$accessToken = $parsedResponse['access_token'];
        }

        return static::$accessToken;
    }
}
