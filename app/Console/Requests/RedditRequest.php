<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Console\Requests;

use DateTime;
use GuzzleHttp\Client;

class RedditRequest
{
    public const TIME_FIRST_POST = 1426668291;

    /**
     * @param DateTime|null $after
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getArchiveAfter(?DateTime $after)
    {
        $response = $this->createPushshiftClient()->request('GET', '/reddit/submission/search', [
            'query' => [
                'subreddit' => 'osugame',
                'sort' => 'asc',
                'limit' => 100,
                'after' => $after ?? new DateTime(static::TIME_FIRST_POST),
            ],
        ])->getBody()->getContents();

        return json_decode($response, false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getNewPosts()
    {
        $response = $this->createRedditClient()->request('GET', '/r/osugame/search.json', [
            'query' => [
                'q' => 'flair:Gameplay',
                'sort' => 'new',
                'restrict_sr' => 'on',
                't' => 'all',
            ],
        ])->getBody()->getContents();

        return json_decode($response, false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getComments(string $id)
    {
        $response = $this->createRedditClient()
            ->request('GET', '/r/osugame/comments/' . $id . '.json')
            ->getBody()
            ->getContents();

        return json_decode($response, false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return Client
     */
    private function createPushshiftClient(): Client
    {
        return new Client(['base_uri' => 'https://api.pushshift.io']);
    }

    /**
     * @return Client
     */
    private function createRedditClient(): Client
    {
        return new Client(['base_uri' => 'https://www.reddit.com']);
    }
}
