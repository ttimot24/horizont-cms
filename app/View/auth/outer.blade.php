@extends('layout', [
    'title' => 'Welcome'
])

@section('content')

<div class='bg-dark p-5 text-white mb-5'>
    <div class='container my-5'>
        <div class="row">
            <div class="col-md-2 col-xs-12 text-center">
                <img src='{{ $admin_logo }}' width="150" alt="">
            </div>
            <div class="col-md-4 col-xs-12 text-center">
                <h1 class="pt-3 hero-title">{{ $app_name }}</h1>
                <h3><q><i>Closer to the web</i></q></h3>
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