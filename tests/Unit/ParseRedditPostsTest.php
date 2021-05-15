<?php

namespace Tests\Unit;

use App\Console\Commands\ParseRedditPosts;
use App\Services\Clients\OsuClient;
use App\Services\Clients\RedditClient;
use Faker\Factory;
use ReflectionClass;
use Tests\TestCase;

class ParseRedditPostsTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $parseRedditPostsMock = $this->getMockBuilder(ParseRedditPosts::class)
            ->onlyMethods(['option', 'line'])
            ->getMock();
        $parseRedditPostsMock->method('option')
            ->willReturn(false);
        $parseRedditPostsMock->method('line')
            ->willReturn(null);

        $parseRedditPostsReflection = new ReflectionClass(ParseRedditPosts::class);
        $reflectionRedditClient = $parseRedditPostsReflection->getProperty('redditClient');
        $reflectionRedditClient->setAccessible(true);
        $reflectionRedditClient->setValue($parseRedditPostsMock, $this->mockRedditClient());

        $reflectionOsuClient = $parseRedditPostsReflection->getProperty('osuClient');
        $reflectionOsuClient->setAccessible(true);
        $reflectionOsuClient->setValue($parseRedditPostsMock, $this->mockOsuClient());

        self::assertEquals(0, $parseRedditPostsMock->handle());
    }

    private function mockRedditClient()
    {
        $faker = Factory::create();
        $redditClientMock = $this->getMockBuilder(RedditClient::class)
            ->onlyMethods(['getArchiveAfter', 'getNewPosts', 'getComments'])
            ->getMock();

        $mockArchive = [
            'data' => $this->mockPosts($faker->randomDigitNotZero())
        ];

        $redditClientMock->method('getArchiveAfter')
            ->willReturn($this->toObject($mockArchive));

        $mockNewPosts = [
            'data' => [
                'children' => $this->mockPosts($faker->randomDigitNotZero())
            ]
        ];

        $redditClientMock->method('getNewPosts')
            ->willReturn($this->toObject($mockNewPosts));

        $redditClientMock->method('getComments')
            ->with(self::anything())
            ->willReturnCallback(function ($argument) {
                return $this->toObject($this->mockPostDetails($argument));
            });

        return $redditClientMock;
    }

    private function mockOsuClient()
    {
        $faker = Factory::create();

        $mockPlayer = [
            [
                'user_id' => $faker->randomNumber(9),
                'username' => $faker->regexify('[a-z0-9]{3,12}'),
            ]
        ];

        $osuClientMock = $this->getMockBuilder(OsuClient::class)
            ->onlyMethods(['getUser'])
            ->getMock();
        $osuClientMock->method('getUser')
            ->willReturn($this->toObject($mockPlayer));

        return $osuClientMock;
    }

    private function mockPosts(int $amount): array
    {
        $faker = Factory::create();
        $posts = [];

        for ($i = 0; $i < $amount; $i++) {
            $title = $faker->regexify('[a-z0-9]{3,12}') . ' | '
                . $faker->word . ' - '
                . $faker->sentence(5, true) . ' ['
                . $faker->word . '] '
                . $faker->sentence(10, true);

            $posts[] = [
                'data' => [
                    'id' => $faker->regexify('[a-z0-9]{6}'),
                    'created_utc' => $faker->unixTime(),
                    'title' => $title,
                    'permalink' => $faker->url,
                ],
            ];
        }

        return $posts;
    }

    private function mockPostDetails(string $id): array
    {
        $faker = Factory::create();

        return [
            [
                'data' => [
                    'children' => [
                        [
                            'data' => [
                                'id' => $id,
                                'author' => $faker->regexify('[A-Za-z0-9_-]{3,20}'),
                                'created_utc' => $faker->unixTime(),
                                'score' => $faker->randomNumber(5),
                                'upvote_ratio' => $faker->randomFloat(2,0,1),
                                'gildings' => [
                                    'gid_1' => $faker->randomNumber(4),
                                    'gid_2' => $faker->randomNumber(4),
                                    'gid_3' => $faker->randomNumber(4),
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    private function toObject(array $data) {
        return json_decode(json_encode($data), false, 512, JSON_THROW_ON_ERROR);
    }
}
