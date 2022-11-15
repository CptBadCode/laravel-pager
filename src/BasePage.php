<?php

namespace Cptbadcode\LaravelPager;

use Cptbadcode\LaravelPager\Traits\Disabled;
use \Cptbadcode\LaravelPager\Contracts\Disabled as IDisabled;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Str;

abstract class BasePage implements Responsable, IDisabled
{
    use Disabled;

    protected string $title = 'Base Page Title';

    protected string $key;

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

    protected function getLandKey(): string
    {
        return 'page.'.$this->key;
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

    abstract public function toResponse($request);
}
