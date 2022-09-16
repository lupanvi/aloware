<?php

namespace App\Providers;

use App\Interfaces\CommentRepositoryInterface;
use App\Repositories\CommentRepository;
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
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
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
