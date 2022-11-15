<?php
namespace Cptbadcode\LaravelPager;

use Cptbadcode\LaravelPager\Console\Commands\CreatePageCommand;
use Cptbadcode\LaravelPager\Facades\PageFacade;
use Cptbadcode\LaravelPager\Helpers\PageLoader;
use Cptbadcode\LaravelPager\Repositories\PageRepository;
use Cptbadcode\LaravelPager\Services\DisableService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->configureCommands();
        $this->configureHelpers();

        $this->app->singleton('page-service', PageService::class);
    }

    public function boot()
    {
        PageService::pageLoaderUsing(PageLoader::class);
        PageService::pageDisablerUsing(DisableService::class);
        PageService::pageRepositoryUsing(PageRepository::class);

        PageFacade::loadPages();

        PageFacade::attachMiddlewareAll(['web']);

        PageFacade::loadMenu();

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
