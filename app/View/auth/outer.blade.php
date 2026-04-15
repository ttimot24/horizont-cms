@extends('layout', [
    'title' => 'Welcome'
])

@section('content')

<div class='jumbotron'>
    <div class='container my-5'>
        <div class="row">
            <div class="col-md-2 col-xs-12 text-center">
                <img src='{{ $admin_logo }}' width="150" alt="">
            </div>
            <div class="col-md-4 col-xs-12 text-center">
                <h1 class="pt-3">{{ $app_name }}</h1>
                <p><q><i>Closer to the web</i></q></p>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>
</div>

<div>
    @yield('content-outer')
</div>


@endsection