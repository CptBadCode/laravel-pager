<?php

namespace Cptbadcode\LaravelPager\Tests;

use Cptbadcode\LaravelPager\Console\Commands\{CacheMenuCommand,CreatePageCommand,CachePageCommand};
use Cptbadcode\LaravelPager\PageService;
use Cptbadcode\LaravelPager\Views\Components\{Header,Layout,Footer,Body};
use Illuminate\Support\Facades\{Artisan, View, Blade};
use Tests\TestCase;

class ProviderTest extends TestCase
{
    public function test_blade_directive_is_registered()
    {
        $directives = Blade::getCustomDirectives();
        $keys = array_keys($directives);

        $this->assertEquals(
            ['menuDir', 'unlessmenuDir', 'elsemenuDir', 'endmenuDir', 'meta', 'script'],
            $keys
        );
        $this->assertEquals(
            '<?php echo app(\Cptbadcode\LaravelPager\Helpers\TagGenerator::class)->generate(\'meta\', $attributes); ?>',
            $directives['meta']('$attributes')
        );

        $this->assertEquals(
            '<?php echo app(\Cptbadcode\LaravelPager\Helpers\ScriptTagGenerator::class)->generate($scripts); ?>',
            $directives['script']('$scripts')
        );
    }

    public function test_commands_is_registered()
    {
        $commands = Artisan::all();
        $this->assertArrayHasKey('cache:page', $commands);
        $this->assertArrayHasKey('cache:menu', $commands);
        $this->assertArrayHasKey('make:page', $commands);

        $this->assertEquals(CreatePageCommand::class, get_class($commands['make:page']));
        $this->assertEquals(CachePageCommand::class, get_class($commands['cache:page']));
        $this->assertEquals(CacheMenuCommand::class, get_class($commands['cache:menu']));
    }

    public function test_helpers_is_registered()
    {
        $this->assertTrue(function_exists('get_class_from_file'));
        $this->assertTrue(function_exists('resolve_model_params_for_route'));
        $this->assertTrue(function_exists('array_some'));
        $this->assertTrue(function_exists('array_every'));
        $this->assertTrue(function_exists('parse_attributes'));
    }

    public function test_components_is_registered()
    {
        $this->assertTrue(View::exists('laravel-pager::components.layout'));
        $this->assertTrue(View::exists(PageService::DEFAULT_TEMPLATE.'.'.PageService::DEFAULT_HEADER));
        $this->assertTrue(View::exists(PageService::DEFAULT_TEMPLATE.'.'.PageService::DEFAULT_BODY));
        $this->assertTrue(View::exists(PageService::DEFAULT_TEMPLATE.'.'.PageService::DEFAULT_FOOTER));
        $this->assertTrue(View::exists('laravel-pager::components.menu.index'));
        $this->assertTrue(View::exists('laravel-pager::components.menu.item'));


        $aliases = Blade::getClassComponentAliases();

        $this->assertEquals(Header::class, $aliases['header']);
        $this->assertEquals(Footer::class, $aliases['footer']);
        $this->assertEquals(Body::class, $aliases['body']);
        $this->assertEquals(Layout::class, $aliases['layout']);
        $this->assertEquals('menu', $aliases['menu']);
    }
}
