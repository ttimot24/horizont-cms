<html>

<head>
    <base href="{{ config('app.url') }}" />
    <title>{{ trans('File Manager') }} - {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @if(auth()->check())
        <meta name="api-token" content="{{ auth()->user()->api_token }}" />
    @endif
    <link rel="shortcut icon" type="image/png" href="resources/images/icons/favicon16.png" />

    @if(!empty(config('horizontcms.vite.entrypoints')))
        @vite(config('horizontcms.vite.entrypoints'), config('horizontcms.vite.build_directory', 'resources'))
    @else
        @foreach (config('horizontcms.css', []) as $each_css)
            <link rel="stylesheet" type="text/css" href="{{ url($each_css) }}">
        @endforeach

        @foreach (config('horizontcms.js', []) as $each_js)
            <script type="text/javascript" src="{{ asset($each_js) }}" defer></script>
        @endforeach
    @endif

        @yield('head')

        @foreach ($jsplugins as $each_js)
            <script type="text/javascript" src="{{ asset($each_js) }}" defer></script>
        @endforeach

</head>

<body>
    <div id="hcms">
        @include('media.filemanager', ['mode' => 'embed'])
    </div>

</body>

</html>
