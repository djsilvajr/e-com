<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Price\SaveProductPriceHistoryEvent;
use App\Events\User\UserRegisteredSendEmailEvent;
use App\Listeners\Price\SaveProductPriceHistoryListener;
use App\Listeners\User\UserRegisteredSendEmailListener;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserRegisteredSendEmailEvent::class => [
            UserRegisteredSendEmailListener::class,
        ],
        SaveProductPriceHistoryEvent::class => [
            SaveProductPriceHistoryListener::class,
        ],
    ];

    public function register(): void {}

    public function boot(): void {}
}
