<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Services\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;

class RedditClient
{
    public const TIME_FIRST_POST = 1426668291;

    private int $rateLimitReset = 0;

    /**
     * @param int|null $after
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getArchiveAfter(?int $after)
    {
        $response = $this->createPushshiftClient()->request('GET', '/reddit/submission/search', [
            'query' => [
                'subreddit' => 'osugame',
                'sort' => 'asc',
                'limit' => 100,
                'after' => $after ?? static::TIME_FIRST_POST,
            ],
        ])->getBody()->getContents();

        return json_decode($response, false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $accessToken
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getNewPosts(string $accessToken)
    {
        $response = $this->createRedditClient()->request('GET', '/r/osugame/new.json', [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
            ],
        ])->getBody()->getContents();

        return json_decode($response, false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $accessToken
     * @param string $after
     * @param string $t
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getTopPosts(string $accessToken, string $after = '', string $t = 'year')
    {
        $response = $this->createRedditClient()->request('GET', '/r/osugame/top.json', [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
            ],
            'query' => [
                'after' => $after,
                't' => $t,
                'limit' => '100',
            ],
        ])->getBody()->getContents();

        return json_decode($response, false, 512, JSON_THROW_ON_ERROR);
    }

    public function getPostsForAuthor(string $accessToken, string $author, string $after = '', string $sort = 'top', string $t = 'all')
    {
        $response = $this->createRedditClient()->request('GET', '/user/' . $author . '/submitted.json', [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
            ],
            'query' => [
                'after' => $after,
                't' => $t,
                'limit' => '100',
                'sort' => $sort,
            ],
        ])->getBody()->getContents();

        return json_decode($response, false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $id
     * @param string $accessToken
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getComments(string $id, string $accessToken)
    {
        try {
            $response = $this->createRedditClient()
                ->request('GET', '/r/osugame/comments/' . $id . '.json', [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                    ],
                ]);

            $ratelimitRemaining = (int)$response->getHeader('x-ratelimit-remaining')[0];
            if ($ratelimitRemaining === 0) {
                $ratelimitReset = (int)$response->getHeader('x-ratelimit-reset')[0];
                sleep($ratelimitReset);
            }

            $content = $response->getBody()
                                ->getContents();
        } catch (ServerException $serverException) {
            return null;
        }

        return json_decode($content, false, 512, JSON_THROW_ON_ERROR);
    }

    public function getAccessToken(): string
    {
        $response = $this->createRedditClient()->request('POST', '/api/v1/access_token', [
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
            'auth' => [
                env('REDDIT_CLIENT_ID'),
                env('REDDIT_CLIENT_SECRET'),
            ],
        ])->getBody()->getContents();

        return json_decode($response, false, 512, JSON_THROW_ON_ERROR)->access_token;
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
        $this->runPreRequestHook();

        $stack = HandlerStack::create();
        $stack->push(Middleware::mapResponse(function (ResponseInterface $response) {
            $this->runPostResponseHook($response);

            return $response;
        }));

        return new Client([
            'base_uri' => 'https://oauth.reddit.com',
            'headers' => [
                'User-Agent' => 'laravel:Andrusowski/ppvr-web (by /u/Andruz)',
            ],
            'handler' => $stack,
        ]);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return void
     */
    private function runPostResponseHook(\Psr\Http\Message\ResponseInterface $response): void
    {
        if ($response->hasHeader('x-ratelimit-remaining')) {
            //echo($response->getHeader('x-ratelimit-remaining')[0] . "\n"); // Remove after debugging
        }
        if ($response->hasHeader('x-ratelimit-remaining') && (int)$response->getHeader('x-ratelimit-remaining')[0] < 10) {
            $this->rateLimitReset = (int)$response->getHeader('x-ratelimit-reset')[0];
        }
    }

    /**
     * @return void
     */
    private function runPreRequestHook(): void
    {
        if ($this->rateLimitReset > 0) {
            //echo('Sleeping for ' . ($this->rateLimitReset + 1) . " seconds\n"); // Remove after debugging
            sleep($this->rateLimitReset + 1);
            $this->rateLimitReset = 0;
        }
    }
}
