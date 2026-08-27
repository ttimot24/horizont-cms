<!DOCTYPE html>
<html>

<head>
    <base href="{{ config('app.url') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    @if (auth()->check())
        <meta name="api-token" content="{{ auth()->user()->api_token }}" />
    @endif

    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <title>{{ $title }} - {{ config('app.name') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('resources/images/icons/favicon16.png') }}" />

    @foreach ($css as $each_css)
        <link rel="stylesheet" type="text/css" href="{{ asset($each_css) }}">
    @endforeach

    @yield('head')

    @foreach ($js as $each_js)
        <script type="text/javascript" src="{{ asset($each_js) }}" defer></script>
    @endforeach

    @if (isset($jsplugins))
        @foreach ($jsplugins as $each_js)
            <script type="text/javascript" src="{{ asset($each_js) }}" defer></script>
        @endforeach
    @endif

</head>

<body @style(['padding-top: 5rem;' => Auth::user()])>

    <div id="hcms">

        @if (!Auth::guest())
            @include('navbar')
            @include('messages')
            <lock-screen :user='@json(Auth::user())' ref="lockscreen"></lock-screen>
        @endif



        @yield('content')


        <footer id='footer'>
            <div class='container'>
                <div class="row py-5 px-3">
                    <div class='col-lg-6 col-sm-12 text-center text-lg-start'>
                        <p class='text-muted credit mb-0'>
                            {{ config('app.name') }} &copy 2015 - {{ date('Y') }}
                            <a href='{{ config('horizontcms.creator.twitter') }}'
                                class="me-1">{{ config('horizontcms.creator.name') }}</a>
                            <a href='{{ config('horizontcms.creator.github') }}'><i style='font-size: 1.2em'
                                    class="fab fa-github" aria-hidden="true"></i></a>
                        </p>
                    </div>
                    <div class='col-lg-6 col-sm-12 text-center text-lg-end'>
                        {{ trans('Version') }}: {{ config('horizontcms.version') }}
                    </div>
                </div>
            </div>
        </footer>
    </div>

</body>

</html>
