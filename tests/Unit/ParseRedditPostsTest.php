<?php

namespace Tests\Unit;

use App\Console\Commands\ParseRedditPosts;
use App\Console\Requests\OsuRequest;
use App\Console\Requests\RedditRequest;
use Faker\Factory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ParseRedditPostsTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $parseRedditPostsCommand = new ParseRedditPosts();

        $parseRedditPostsReflection = new ReflectionClass($parseRedditPostsCommand);
        $reflectionRedditClient = $parseRedditPostsReflection->getProperty('redditRequest');
        $reflectionRedditClient->setAccessible(true);
        $reflectionRedditClient->setValue($parseRedditPostsCommand, $this->mockRedditClient());

        $reflectionOsuClient = $parseRedditPostsReflection->getProperty('osuRequest');
        $reflectionOsuClient->setAccessible(true);
        $reflectionOsuClient->setValue($parseRedditPostsCommand, $this->mockOsuClient());

        $parseRedditPostsCommand->addOption()
        $parseRedditPostsCommand->handle();
    }

    private function mockRedditClient()
    {
        $faker = Factory::create();
        $mockRedditClient = $this->getMockBuilder(RedditRequest::class)
            ->onlyMethods(['getArchiveAfter', 'getNewPosts', 'getComments'])
            ->getMock();

        $mockArchive = [
            'data' => $this->mockPosts($faker->randomDigitNotZero())
        ];

        $mockRedditClient->method('getArchiveAfter')
            ->willReturn((object)$mockArchive);

        $mockNewPosts = [
            'data' => [
                'children' => [
                    $this->mockPosts($faker->randomDigitNotZero())
                ]
            ]
        ];

        $mockRedditClient->method('getNewPosts')
            ->willReturn((object)$mockNewPosts);

        $mockRedditClient->method('getComments')
            ->willReturn((object)$this->mockPostDetails());

        return $mockRedditClient;
    }

    private function mockOsuClient()
    {
        $faker = Factory::create();

        $mockPlayer = [
            [
                'user_id' => $faker->randomNumber(9),
                'username' => $faker->regexify('[a-z0-9]){3,12}'),
            ]
        ];

        return $this->getMockBuilder(OsuRequest::class)
            ->onlyMethods(['getUser'])
            ->getMock()
            ->method('getUser')
            ->willReturn((object) $mockPlayer);
    }

    private function mockPosts(int $amount): array
    {
        $faker = Factory::create();
        $posts = [];

        for ($i = 0; $i < $amount; $i++) {
            $posts[] = [
                'id' => $faker->regexify('([a-z0-9]){6}'),
                'created_utc' => $faker->unixTime(),
            ];
        }

        return $posts;
    }

    private function mockPostDetails(): array
    {
        $faker = Factory::create();

        $title = $faker->regexify('[a-z0-9]){3,12}') . ' | '
            . $faker->word . ' - '
            . $faker->sentence(5, true) . ' ['
            . $faker->word . '] '
            . $faker->sentence(10, true);

        return [
            [
                'data' => [
                    'children' => [
                        [
                            'data' => [
                                'id' => $faker->regexify('([a-z0-9]){6}'),
                                'title' => $title,
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
}
