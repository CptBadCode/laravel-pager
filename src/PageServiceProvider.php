<?php
namespace Cptbadcode\LaravelPager;

use Cptbadcode\LaravelPager\Console\Commands\CreatePageCommand;
use Cptbadcode\LaravelPager\Repositories\MenuRepository;
use Cptbadcode\LaravelPager\Repositories\PageRepository;
use Cptbadcode\LaravelPager\Views\Components\Body;
use Cptbadcode\LaravelPager\Views\Components\Footer;
use Cptbadcode\LaravelPager\Views\Components\Header;
use Cptbadcode\LaravelPager\Views\Components\Layout;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class PageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->configureCommands();
        $this->configureHelpers();
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-pager');

        PageService::pageRepositoryUsing(PageRepository::class);
        PageService::menuRepositoryUsing(MenuRepository::class);

        PageService::loadPages();
        PageService::applyMiddlewareAll(['web']);

        $this->configureRoutes();
        $this->configurePublishing();
        $this->configureComponents();
    }

    private function configureCommands()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            CreatePageCommand::class
        ]);
    }

    private function configureHelpers()
    {
        $file = __DIR__.'/Helpers/Helpers.php';
        if (file_exists($file)) {
            require_once($file);
        }
    }

    public function configurePublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-pager'),
        ], 'laravel-pager-views');
    }

    public function configureComponents()
    {
        Blade::componentNamespace('Cptbadcode\\LaravelPager\\Views\\Components', 'laravel-pager');
        $this->callAfterResolving(BladeCompiler::class, function () {
            Blade::component('layout', Layout::class);

            $dynamicComponents = ['header' => Header::class, 'body' => Body::class, 'footer' => Footer::class];
            foreach ($dynamicComponents as $tag => $component) {
                Blade::component($tag, $component);
                PageService::addGlobalComponent($tag);
            }
        });
    }

    private function configureRoutes()
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        Route::group([
            'namespace' => 'Cptbadcode\LaravelPager\Http\Controllers',
            'domain' => null,
            'prefix' => config('laravelPager.prefix')
        ], function() {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }
}
