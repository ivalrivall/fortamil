<?php

namespace App\Providers;

use App\Interfaces\AddressRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\BaseRepositoryInterface;
use App\Interfaces\CloudinaryRepositoryInterface;
use App\Interfaces\RegionRepositoryInterface;
use App\Interfaces\StoreRepositoryInterface;
use App\Repositories\AddressRepository;
use App\Repositories\CloudinaryRepository;
use App\Repositories\RegionRepository;
use App\Repositories\StoreRepository;
use App\Repositories\UserRepository;
use App\Repository\BaseRepository;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RegionRepositoryInterface::class, RegionRepository::class);
        $this->app->bind(StoreRepositoryInterface::class, StoreRepository::class);
        $this->app->bind(AddressRepositoryInterface::class, AddressRepository::class);
        $this->app->bind(CloudinaryRepositoryInterface::class, CloudinaryRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
