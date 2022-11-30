<?php
namespace Cptbadcode\LaravelPager;


use Cptbadcode\LaravelPager\Contracts\Menu\IMenuDirectory;
use Cptbadcode\LaravelPager\Services\MenuService;
use Cptbadcode\LaravelPager\Actions\{MenuRemover, MenuUpdater};
use Cptbadcode\LaravelPager\Console\Commands\CreatePageCommand;
use Cptbadcode\LaravelPager\Repositories\{MenuRepository, PageRepository};
use Cptbadcode\LaravelPager\Views\Components\{Body, Footer, Header, Layout};
use Illuminate\Support\Facades\{Blade, Route};
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class PageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->configureCommands();
        $this->configureHelpers();
        $this->configureBlade();
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-pager');

        PageService::pageRepositoryUsing(PageRepository::class);
        MenuService::menuRepositoryUsing(MenuRepository::class);
        MenuService::menuUpdaterUsing(MenuUpdater::class);
        MenuService::menuRemoverUsing(MenuRemover::class);

        PageService::loadPages();
        PageService::applyMiddlewareAll(['web']);

        $this->configureRoutes();
        $this->configurePublishing();
        $this->configureComponents();
    }

    private function configureBlade()
    {
        Blade::directive('meta', function ($attributes) {
            return "<?php echo app(\Cptbadcode\LaravelPager\Helpers\TagGenerator::class)->generate('meta', $attributes); ?>";
        });

        Blade::directive('script', function ($scripts) {
            return "<?php echo app(\Cptbadcode\LaravelPager\Helpers\ScriptTagGenerator::class)->generate($scripts); ?>";
        });
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
            __DIR__.'/../resources/views/components/layouts' => resource_path('views/vendor/laravel-pager'),
        ], 'laravel-pager-components');

        $this->publishes([
            __DIR__.'/../resources/views/partials' => resource_path('views/partials'),
        ], 'laravel-pager-views');
    }

    public function configureComponents()
    {
        Blade::componentNamespace('Cptbadcode\\LaravelPager\\Views\\Components', 'laravel-pager');

        Blade::if('menuDir', fn($value) => $value instanceof IMenuDirectory);

        $this->callAfterResolving(BladeCompiler::class, function () {
            Blade::component('layout', Layout::class);
            Blade::component('menu', 'menu');

            $dynamicComponents = ['header' => Header::class, 'body' => Body::class, 'footer' => Footer::class];
            foreach ($dynamicComponents as $tag => $component) {
                Blade::component($tag, $component);
                PageService::addDynamicComponent($tag);
            }
        });
    }

    /**
     * Register the given component.
     *
     * @param  string  $component
     * @return void
     */
    protected function registerComponent(string $component)
    {
        Blade::component('laravel-pager::components.templates.'.$component, 'lp-'.$component);
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
