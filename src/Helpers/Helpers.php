<?php

use Illuminate\Support\Str;

if (!function_exists('get_class_from_file')) {
    function get_class_from_file(\SplFileInfo $file, string $extension = 'php'): string
    {
        return Str::replace([base_path(), '.'.$extension], '', $file->getPathname());
    }
}
