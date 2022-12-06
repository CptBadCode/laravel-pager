<?php

namespace Cptbadcode\LaravelPager\Traits;

trait PageMiddleware
{
    protected array $middleware = [];

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function setMiddleware(array $middleware = [])
    {
        $this->middleware = $middleware;
    }

    public function addMiddleware(array $middleware = [])
    {
        $this->middleware = array_merge($this->middleware, $middleware);
    }
}
