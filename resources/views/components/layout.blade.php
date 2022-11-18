@props([
    'page',
])

<html lang="{{ str_replace('_', '-', $page['lang']) }}">
<head>

    <title>{{ $page['title'] ?? config('app.name', 'Laravel') }}</title>

    <meta name="description" content="{{ $page['description'] }}">

    @foreach($page['meta'] as $tag)
        <meta name="{{$tag['name']}}" content="{{ $tag['content'] }}">
    @endforeach

    @stack('styles')
    @stack('scripts')
</head>
    <body class="antialiased">
        {{ $slot }}

        @stack('footer_scripts')
    </body>
</html>
