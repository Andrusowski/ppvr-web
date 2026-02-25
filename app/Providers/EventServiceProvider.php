<?php

namespace App\Providers;

use App\Services\Socialite\OsuProvider;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->bootOsuSocialite();
    }

    /**
     * Register the osu! Socialite provider.
     */
    protected function bootOsuSocialite(): void
    {
        $socialite = $this->app->make(SocialiteFactory::class);

        $socialite->extend('osu', function ($app) use ($socialite) {
            $config = $app['config']['services.osu'];

            return $socialite->buildProvider(OsuProvider::class, $config);
        });
    }
}
