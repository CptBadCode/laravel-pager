@props([
    'page',
])

<html lang="{{ str_replace('_', '-', $page['lang']) }}">
<head>

    <title>{{ $page['title'] ?? config('app.name', 'Laravel') }}</title>

    @meta($page['meta'])

    @if(count($page['styles']))
        @vite($page['styles'])
    @endif

    @if(count($page['scripts']))
        @vite($page['scripts'])
    @endif

</head>
    <body class="antialiased">
        {{ $slot }}

        @if(count($page['footer_scripts']))
            @vite($page['footer_scripts'])
        @endif
        @script($page['public_scripts'])
    </body>
</html>
