<?php

namespace Cptbadcode\LaravelPager\Traits;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Routing\ControllerDispatcher;

trait AdditionAction
{
    protected string|null $action = null;

    protected mixed $actionResult = [];

    public function hasActionToCall(): bool
    {
        return !!$this->action;
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
            resolve_model_params_for_route($this->action, $route);
            $this->actionResult = $dispatcher->dispatch($route, $controller, 'handle');
            return $this->actionResult;
        }

        throw new \BadMethodCallException('Метода и контроллера для вызова не найдено');
    }
}
