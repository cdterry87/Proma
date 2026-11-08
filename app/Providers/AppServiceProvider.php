<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Client;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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

        $this->secureLivewireRequests();
    }

    /**
     * Every Livewire action posts to one endpoint, outside the page's route.
     */
    private function secureLivewireRequests(): void
    {
        // Livewire reapplies a page's middleware to its actions only when the
        // middleware is on this list. Without it, a session that a password
        // change ended can still run actions until it loads a full page.
        Livewire::addPersistentMiddleware([AuthenticateSession::class]);

        // A ceiling for scripted requests, well above what a person can click.
        RateLimiter::for('livewire', fn (Request $request) => Limit::perMinute(180)
            ->by($request->user()?->getAuthIdentifier() ?? $request->ip()));

        Livewire::setUpdateRoute(fn (array $handle, string $path) => Route::post($path, $handle)
            ->middleware(['web', 'throttle:livewire']));
    }
}
