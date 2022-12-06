<?php

namespace Cptbadcode\LaravelPager;

use Cptbadcode\LaravelPager\Contracts\IPage;
use Cptbadcode\LaravelPager\Menu\MenuItem;
use Cptbadcode\LaravelPager\Services\MenuService;
use Cptbadcode\LaravelPager\Traits\{AdditionAction, Disabled, PageMiddleware, TemplatesPage};
use \Cptbadcode\LaravelPager\Contracts\Disabled as IDisabled;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class BasePage implements Responsable, IDisabled, IPage
{
    use Disabled, TemplatesPage, PageMiddleware, AdditionAction;

    public static string $key;

    /**
     * Position in menu
     * @var int
     */
    public int $sortKeyInMenu = 0;

    protected ?string
        $title = 'Base Page Title';

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

    public string $uri = '/';

    protected bool $isCanAddToMenu = true;

    public function get(string $key): mixed
    {
        return $this->{$key} ?? false;
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
        return PageService::$localeTitle ? __($this->getLandKey()) : $this->title;
    }

    public function forMenu(): MenuItem
    {
        return new MenuItem(
            title: $this->title,
            uri: $this->uri,
            key: $this->getKey(),
            isDisabled: $this->disabled,
            sortKey: $this->sortKeyInMenu
        );
    }

    public function __toString(): string
    {
        return json_encode($this->forMenu()->toArray());
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
            'title' => $this->getTitle(),
            'lang' => App::getLocale(),
            'header_layout' => $this->getHeaderLayout(),
            'footer_layout' => $this->getFooterLayout(),
            'body_layout' => $this->getBodyLayout(),
            'is_auth' => auth()->check(),
            'user' => auth()->user(),
            'menu' => MenuService::repository()->getMenu() ?? []
        ];
    }

    abstract public function renderData($request): array;
}
