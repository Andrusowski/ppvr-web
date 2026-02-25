<?php

namespace App\Services\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class OsuProvider extends AbstractProvider
{
    /**
     * The scopes being requested.
     *
     * @var array<int, string>
     */
    protected $scopes = ['identify', 'public'];

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * Get the authentication URL for the provider.
     *
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://osu.ppy.sh/oauth/authorize', $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl(): string
    {
        return 'https://osu.ppy.sh/oauth/token';
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param string $token
     * @return array<string, mixed>
     */
    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get('https://osu.ppy.sh/api/v2/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param array<string, mixed> $user
     * @return User
     */
    protected function mapUserToObject(array $user): User
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => $user['username'],
            'name' => $user['username'],
            'email' => null,
            'avatar' => $user['avatar_url'],
        ]);
    }
}
