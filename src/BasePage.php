<?php

namespace Cptbadcode\LaravelPager;

use Cptbadcode\LaravelPager\Contracts\IPage;
use Cptbadcode\LaravelPager\Menu\MenuItem;
use Cptbadcode\LaravelPager\Services\MenuService;
use Cptbadcode\LaravelPager\Traits\Disabled;
use \Cptbadcode\LaravelPager\Contracts\Disabled as IDisabled;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class BasePage implements Responsable, IDisabled, IPage
{
    use Disabled;

    public static string
        $key;

    protected string
        $title = 'Base Page Title';

    protected string
        $body = PageService::DEFAULT_BODY_COMPONENT,
        $header = PageService::DEFAULT_HEADER_COMPONENT,
        $footer = PageService::DEFAULT_FOOTER_COMPONENT;

    protected array
        $styles = ['resources/css/app.css'],
        $scripts = ['resources/js/app.js'],
        $footer_scripts = [],
        $publicScripts = [];

    protected array $meta = [
        ['name' => 'charset', 'content' => 'utf-8'],
        ['name' => 'description', 'content' => 'this is description page'],
        ['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1'],
    ];

    protected array $middleware = [];

    public string $uri = '/';

    public bool $lang = true;

    protected bool $isCanAddToMenu = true;

    protected string|null $action = null;

    protected array $actionResult = [];

    public function get(string $key): mixed
    {
        return $this->{$key} ?? false;
    }

    public function getHeaderLayout(): string
    {
        return PageService::headerForPage($this->getKey()) ?? $this->header;
    }

    public function getFooterLayout(): string
    {
        return PageService::footerForPage($this->getKey()) ?? $this->footer;
    }

    public function getKey(): string
    {
        return static::$key;
    }

    public function setKey(string $value)
    {
        static::$key = Str::snake($value);
    }

    public function canAddToMenu(): bool
    {
        return $this->isCanAddToMenu;
    }

    public function getTitle(): string
    {
        return $this->lang ? __($this->getLandKey()) : $this->title;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function setMiddleware(array $middleware = [])
    {
        $this->middleware = $middleware;
    }

    public function hasActionToCall(): bool
    {
        return !!$this->action;
    }

    public function forMenu(): MenuItem
    {
        return new MenuItem(
            title: $this->title,
            uri: $this->uri,
            key: $this->getKey(),
            isDisabled: $this->disabled
        );
    }

    public function __toString(): string
    {
        return json_encode($this->forMenu());
    }

    public function toResponse($request)
    {
        return view(PageService::ROOT_VIEW)->with(
            [
                'page' => array_merge(
                    $this->sharedData(),
                    $this->renderData($request)
                )
            ]
        );
    }

    protected function getLandKey(): string
    {
        return PageService::LANG_FILE.'.'.static::$key;
    }

    private function sharedData(): array
    {
        return [
            'styles' => $this->styles,
            'scripts' => $this->scripts,
            'public_scripts' => $this->publicScripts,
            'footer_scripts' => $this->footer_scripts,
            'meta' => $this->meta,
            'title' => $this->title,
            'lang' => App::getLocale(),
            'header_layout' => $this->getHeaderLayout(),
            'footer_layout' => $this->getFooterLayout(),
            'body_layout' => $this->body,
            'is_auth' => auth()->check(),
            'user' => auth()->user(),
            'menu' => MenuService::repository()->getMenu()?->toArray() ?? []
        ];
    }

    /**
     * Запустить дополнительный обработчик маршрута
     * @param Container $container
     * @param $route
     * @return mixed
     * @throws BindingResolutionException
     */
    public function callAction(Container $container, $route): mixed
    {
        if ($this->hasActionToCall()) {
            $controller = $container->make(ltrim($this->action, '\\'));
            $dispatcher = new ControllerDispatcher($container);
            resolve_model_params_for_route($this->action, 'handle', $route);
            $this->actionResult = $dispatcher->dispatch($route, $controller, 'handle');
            return $this->actionResult;
        }

        throw new \BadMethodCallException('Метода и контроллера для вызова не найдено');
    }

    abstract public function renderData($request): array;
}
