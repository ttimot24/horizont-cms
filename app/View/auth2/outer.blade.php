@extends('layout', [
    'title' => 'Welcome'
])

@section('content')

<section style="margin-bottom: -7.5rem;" class="min-vh-100 d-flex align-items-center">
    <div class="container-fluid h-100 p-0">
        <div class="row g-0 h-100">

            <div class="col-12 col-md-7 d-none d-md-flex align-items-center justify-content-center 
                        min-vh-100 min-vh-md-100"
                 style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), 
                        url('https://picsum.photos/seed/{{ floor(time() / (1 * 60))}}/1024/768.webp') center/cover no-repeat;">
                
                @isset($admin_logo)
                    <div class="text-center text-white p-4 d-none d-md-block">
                        <img src="{{ $admin_logo }}" 
                            class="img-fluid rounded mb-4" 
                            width="320" 
                            alt="Logo">
                    </div>
                @endisset
            </div>

            <div class="col-12 col-md-5 d-flex align-items-center bg-white">
                @yield('content-outer')
            </div>

        </div>
    </div>
</section>


@endsection