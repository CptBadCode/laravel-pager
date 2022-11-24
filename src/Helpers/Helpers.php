<?php

use Cptbadcode\LaravelPager\Helpers\BindingParamFromRoute;
use Illuminate\Support\Str;

if (!function_exists('get_class_from_file')) {
    function get_class_from_file(\SplFileInfo $file, string $extension = 'php'): string
    {
        return Str::replace([base_path(), '.'.$extension], '', $file->getPathname());
    }
}

if (!function_exists('resolve_model_params_for_route'))
{
    function resolve_model_params_for_route(object|string $class, string $method, $route)
    {
        $params = (new \ReflectionMethod($class, $method))->getParameters();
        BindingParamFromRoute::setParams($params);
        BindingParamFromRoute::resolveForRoute(app(), $route);
        return $route;
    }
}


if (!function_exists('array_some')) {
    /**
     *
     * @param callable $callback
     * @param array $arr
     * @return bool
     */
    function array_some(callable $callback, array $arr): bool
    {
        foreach ($arr as $key => $element) {
            if ($callback($element, $key)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('array_every')) {
    /**
     *
     * @param callable $callback
     * @param array $arr
     * @return bool
     */
    function array_every(callable $callback, array $arr): bool
    {
        foreach ($arr as $element) {
            if (!$callback($element)) {
                return false;
            }
        }
        return true;
    }
}