<x-layout :page="$page">
    @if(isset($page['styles']))
        @foreach($page['styles'] as $style)
            @push('styles')
                <link href="{{ asset($style) }}" rel="stylesheet">
            @endpush
        @endforeach
    @endif

    @if(isset($page['scripts']))
        @foreach($page['scripts'] as $script)
            @push('scripts')
                <script type="text/javascript" src="{{ asset($script) }}"></script>
            @endpush
        @endforeach
    @endif

    @if(isset($page['footer_scripts']))
        @foreach($page['footer_scripts'] as $footer_script)
            @push('footer_scripts')
                <script type="text/javascript" src="{{ asset($footer_script) }}"></script>
            @endpush
        @endforeach
    @endif

    @foreach(\Cptbadcode\LaravelPager\PageService::$globalComponents as $componentName)
        <x-dynamic-component :component="$componentName" :page="$page"></x-dynamic-component>
    @endforeach
</x-layout>
