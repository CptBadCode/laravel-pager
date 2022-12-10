<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

if (!function_exists('get_class_from_file')) {
    function get_class_from_file(\SplFileInfo $file, string $extension = 'php'): string
    {
        return Str::replace([base_path(), '.'.$extension], '', $file->getPathname());
    }
}

if (!function_exists('resolve_model_params_for_route'))
{
    function resolve_model_params_for_route(object|string $class, $route)
    {
        $class = new \ReflectionClass($class);
        $route->action = [
            "domain" => null,
            "uses" => "$class->name@handle",
            "controller" => $class->name,
            "namespace" => $class->getNamespaceName(),
            "prefix" => "",
            "where" => [],
            "middleware" => []
        ];
        \Illuminate\Routing\ImplicitRouteBinding::resolveForRoute(app(), $route);
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

if (!function_exists('parse_attributes')) {
    /**
     * Parse the attributes into key="value" strings.
     *
     * @param $attributes
     * @return string
     */
    function parse_attributes($attributes): string
    {
        return implode('', Collection::make($attributes)
            ->reject(fn ($value, $key) => in_array($value, [false, null], true))
            ->flatMap(fn ($value, $key) => $value === true ? [$key] : [$key => $value])
            ->map(fn ($value, $key) => is_int($key) ? $value : $key.'="'.$value.'"')
            ->values()
            ->all());
    }
}
