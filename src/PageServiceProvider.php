<?php
namespace Cptbadcode\LaravelPager;

use Cptbadcode\LaravelPager\Console\Commands\CreatePageCommand;
use Cptbadcode\LaravelPager\Repositories\MenuRepository;
use Cptbadcode\LaravelPager\Repositories\PageRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->configureCommands();
        $this->configureHelpers();
    }

    public function boot()
    {
        PageService::pageRepositoryUsing(PageRepository::class);
        PageService::menuRepositoryUsing(MenuRepository::class);

        PageService::loadPages();
        PageService::applyMiddlewareAll(['web']);

        $this->configureRoutes();
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

    private function configureRoutes()
    {
        Route::group([
            'namespace' => 'Cptbadcode\LaravelPager\Http\Controllers',
            'domain' => null,
            'prefix' => config('laravelPager.prefix')
        ], function() {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }
}
