<?php

namespace Cptbadcode\LaravelPager\Helpers;

use Illuminate\Support\HtmlString;

class TagGenerator
{
    public function generate(string $tagName, array $attributes): string
    {
        $str = "";
        if (!count($attributes)) return $str;
        $isMulti = is_array($attributes[0]);

        $generateTag = function(string &$result, string $tagName, array $attributes) {
            $attributes = parse_attributes($attributes);
            $result .= "<$tagName $attributes ></$tagName>";
        };

        if ($isMulti) {
            foreach ($attributes as $attribute) {
                $generateTag($str, $tagName, $attribute);
            }
        }
        else $generateTag($str, $tagName, $attributes);
        return new HtmlString($str);
    }

}
