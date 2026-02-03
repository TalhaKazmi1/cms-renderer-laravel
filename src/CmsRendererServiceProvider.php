<?php

namespace TalhaKazmi\CmsRenderer;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use TalhaKazmi\CmsRenderer\Console\Commands\InstallCommand;
use TalhaKazmi\CmsRenderer\View\Components\BlogRenderer;
use TalhaKazmi\CmsRenderer\View\Components\BlogList;
use TalhaKazmi\CmsRenderer\View\Components\BlogPost;

class CmsRendererServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/config/cms-renderer.php',
            'cms-renderer'
        );

        // Register the main service
        $this->app->singleton('cms-renderer', function ($app) {
            return new CmsRendererService(
                config('cms-renderer.organization_id'),
                config('cms-renderer.api_url')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/config/cms-renderer.php' => config_path('cms-renderer.php'),
        ], 'cms-renderer-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/cms-renderer'),
        ], 'cms-renderer-views');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'cms-renderer');

        // Register Blade components
        Blade::component('cms-blog-renderer', BlogRenderer::class);
        Blade::component('cms-blog-list', BlogList::class);
        Blade::component('cms-blog-post', BlogPost::class);

        // Register Artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}
