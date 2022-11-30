<x-layout :page="$page">
    @foreach(\Cptbadcode\LaravelPager\PageService::$dynamicComponents as $componentName)
        <x-dynamic-component :component="$componentName" :page="$page"></x-dynamic-component>
    @endforeach
</x-layout>
