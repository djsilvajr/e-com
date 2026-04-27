<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repository\Contracts\FeatureFlagInterface;
use App\Repository\Contracts\PriceHistoryInterface;
use App\Repository\Contracts\PriceInterface;
use App\Repository\Contracts\ProductInterface;
use App\Repository\Contracts\ProductTypeInterface;
use App\Repository\Contracts\ProductVariantInterface;
use App\Repository\Contracts\UserRepositoryInterface;
use App\Repository\Contracts\WelcomeMailerInterface;
use App\Repository\FeatureFlagRepository;
use App\Repository\PriceHistoryRepository;
use App\Repository\PriceRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;
use App\Repository\ProductVariantRepository;
use App\Repository\UserRepository;
use App\Repository\WelcomeMailerRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(FeatureFlagInterface::class, FeatureFlagRepository::class);
        $this->app->bind(ProductTypeInterface::class, ProductTypeRepository::class);
        $this->app->bind(ProductInterface::class, ProductRepository::class);
        $this->app->bind(PriceInterface::class, PriceRepository::class);
        $this->app->bind(ProductVariantInterface::class, ProductVariantRepository::class);
        $this->app->bind(PriceHistoryInterface::class, PriceHistoryRepository::class);
        $this->app->bind(WelcomeMailerInterface::class, WelcomeMailerRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
