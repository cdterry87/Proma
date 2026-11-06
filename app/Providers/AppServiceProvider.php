<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Client;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());

        Relation::enforceMorphMap([
            'client' => Client::class,
            'project' => Project::class,
            'issue' => Issue::class,
            'task' => Task::class,
        ]);

        Blade::anonymousComponentPath(resource_path('views/layouts'), 'layouts');
    }
}
