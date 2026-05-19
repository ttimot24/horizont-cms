@extends('layout', ['title' => trans("Install")])

@section('content')
    <div class="bg-dark text-white py-5">
        <div class='container py-5'>
            <h1 style="font-size:3rem;">Installing {{ config('app.name') }}</h1>

            <div class='progress'>
                <div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0'
                    aria-valuemax='100' style='min-width: 100em;'>
                    100%
                </div>
            </div>
            <hr />

            <h2>Step 4: Finish</h2>
            </br>
            </br>

            @include('messages')

            @if (!session()->has('error'))
                <br>
                <p>
                <h4>Don't forget to set up cron for sheduled jobs:</h4>
                <pre class="bg-white text-center font-weight-bold p-2"><code>* * * * * php {{ base_path('artisan') }} schedule:run >> /dev/null 2>&1</code></pre>
                </p>
            @endif

            @if (!session()->has('error'))
                <br><br>
                <a href="{{ route('login') }}"><button type='button'
                        class='btn btn-primary btn-md animated pulse delay-3s'>Finish & go to admin area</button></a>
            @else
                <a href="{{route('install.show', 'step3')}}"><button type='button' class='btn btn-primary btn-md'><i class="fa fa-repeat"
                            aria-hidden="true"></i> Retry</button></a>
            @endif
        </div>
    </div>
@endsection
