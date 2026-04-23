<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository\Contracts\UserRepositoryInterface;
use App\Repository\Contracts\FeatureFlagInterface;
use App\Repository\Contracts\ProductTypeInterface;
use App\Repository\Contracts\ProductInterface;
use App\Repository\Contracts\PriceInterface;
use App\Repository\Contracts\ProductVariantInterface;
use App\Repository\UserRepository;
use App\Repository\FeatureFlagRepository;
use App\Repository\ProductTypeRepository;
use App\Repository\ProductRepository;
use App\Repository\PriceRepository;
use App\Repository\ProductVariantRepository;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
