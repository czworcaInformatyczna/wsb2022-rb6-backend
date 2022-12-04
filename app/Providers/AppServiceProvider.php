<?php

namespace App\Providers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'asset' => Asset::class,
            'asset_category' => AssetCategory::class,
            'user' => User::class
        ]);
    }
}
