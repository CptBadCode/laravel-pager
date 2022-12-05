<?php

namespace Cptbadcode\LaravelPager\Traits;

trait Caching
{
    public static function cache(array $items): void
    {
        if (self::$cached)
            \Illuminate\Support\Facades\Cache::forever(self::CACHE_KEY, $items);
    }

    public static function enableCache(): static
    {
        self::$cached = true;

        return new static;
    }
}
