<?php

namespace Cptbadcode\LaravelPager;

use Cptbadcode\LaravelPager\Helpers\MenuGenerator;
use Cptbadcode\LaravelPager\Traits\Disabled;
use \Cptbadcode\LaravelPager\Contracts\Disabled as IDisabled;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class BasePage implements Responsable, IDisabled
{
    use Disabled;

    public static string
        $key;

    protected string
        $title = 'Base Page Title',
        $description = 'this is description page',
        $charset = 'utf-8';

    protected string
        $body = PageService::DEFAULT_BODY_COMPONENT,
        $header = PageService::DEFAULT_HEADER_COMPONENT,
        $footer = PageService::DEFAULT_FOOTER_COMPONENT;

    protected array
        $styles = ['base.css'],
        $scripts = [],
        $footer_scripts = [];

    protected array $additionMeta = [
        ['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']
    ];

    protected array $middleware = [];

    public string $uri = '/';

    public bool $lang = true;

    protected bool $isCanAddToMenu = true;

    protected array $action = [];

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

    public function removeFromMenu()
    {
        $this->isCanAddToMenu = false;
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
        return count($this->action) === 2;
    }

    public function toArray(): array
    {
        $menuTemplate = MenuGenerator::getMenuTemplate();
        $menuTemplate[MenuGenerator::$menuTitleKey] = $this->title;
        $menuTemplate[MenuGenerator::$menuUriKey] = $this->uri;
        $menuTemplate[MenuGenerator::$menuDisableKey] = $this->disabled;
        return $menuTemplate;
    }

    public function __toString(): string
    {
        return json_encode($this->toArray());
    }

    public function toResponse($request)
    {
        return view(PageService::ROOT_VIEW)->with(
            array_merge(
                $this->sharedData(),
                $this->renderData($request)
            )
        );
    }

    protected function getLandKey(): string
    {
        return PageService::LANG_FILE.'.'.static::$key;
    }

    protected function sharedData(): array
    {
        return [
            'page' => [
                'styles' => $this->styles,
                'scripts' => $this->scripts,
                'footer_scripts' => $this->footer_scripts,
                'meta' => $this->additionMeta,
                'title' => $this->title,
                'charset' => $this->charset,
                'description' => $this->description,
                'lang' => App::getLocale(),
                'header_layout' => $this->getHeaderLayout(),
                'footer_layout' => $this->getFooterLayout(),
                'body_layout' => $this->body,
                'is_auth' => auth()->check(),
                'user' => auth()->user(),
                'uri' => $this->uri,
            ]
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
            [$class, $method] = $this->action;
            $controller = $container->make(ltrim($class, '\\'));
            $dispatcher = new ControllerDispatcher($container);
            resolve_model_params_for_route($class, $method, $route);
            $this->actionResult = $dispatcher->dispatch($route, $controller, $method);
            return $this->actionResult;
        }

        throw new \BadMethodCallException('Метода и контроллера для вызова не найдено');
    }

    abstract public function renderData($request): array;
}
