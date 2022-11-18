<?php

namespace Cptbadcode\LaravelPager;

use Cptbadcode\LaravelPager\Helpers\BindingParamFromRoute;
use Cptbadcode\LaravelPager\Traits\Disabled;
use \Cptbadcode\LaravelPager\Contracts\Disabled as IDisabled;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class BasePage implements Responsable, IDisabled
{
    use Disabled;

    protected string $title = 'Base Page Title',
                     $key,
                     $description = 'this is description page',
                     $charset = 'utf-8';

    protected string $pageContent = 'body',
                     $header = 'header',
                     $footer = 'footer';

    protected array $styles = ['base.css'],
                    $scripts = [],
                    $footer_scripts = [];

    protected array $additionMeta = [
        ['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']
    ];

    protected array $middleware = [];

    public string $uri = '/';

    public bool $lang = true;

    protected bool $isCanAddToMenu = true;


    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $value)
    {
        $this->key = Str::snake($value);
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

    public function toArray(): array
    {
        return ['title' => $this->title, 'uri' => $this->uri, 'is_disabled' => $this->disabled];
    }

    public function toResponse($request)
    {
        return view('app')->with(
            array_merge(
                $this->sharedData(),
                $this->renderData($request),
                $this->resolveParamsForRoute($request)
            )
        );
    }

    protected function getLandKey(): string
    {
        return 'page.'.$this->key;
    }

    private function sharedData(): array
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
                'header_layout' => $this->header,
                'footer_layout' => $this->footer,
                'body_layout' => $this->pageContent,
                'is_auth' => auth()->check(),
                'user' => auth()->user(),
                'uri' => $this->uri,
            ]
        ];
    }

    protected function resolveParamsForRoute($request): array
    {
        if (method_exists($this, 'prepareResponseData')) {
            $route = $request->route();
            $params = (new \ReflectionMethod($this, 'prepareResponseData'))->getParameters();
            BindingParamFromRoute::setParams($params);
            BindingParamFromRoute::resolveForRoute(app(), $route);
            return $this->prepareResponseData(...$route->parameters());
        }
        return [];
    }

    abstract public function renderData($request): array;
}
