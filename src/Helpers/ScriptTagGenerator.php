<?php

namespace Cptbadcode\LaravelPager\Helpers;

class ScriptTagGenerator
{
    public function generate(array $scripts): string
    {
        $attributes = [];
        foreach ($scripts as $script) {
            $attributes[] = [
                'type' => 'text/javascript',
                'src' => asset($script)
            ];
        }

        return app(TagGenerator::class)->generate('script', $attributes);
    }
}
