@extends('layout', ['title' => trans('dashboard.title')])

@section('content')
    <div class='container main-container' id='dashboard'>

        <div class='row'>
            <div class='col m-auto'>

                <h3 class="text-center">{{ $domain }}</h3>
                <p class='text-muted text-center'>{{ trans('dashboard.server_ip') . ' ' . $server_ip }}</p>
                <p class='text-muted text-center'>{{ trans('dashboard.client_ip') . ' ' . $client_ip }}</p>

                <hr>

                <div class='col-md-10 col-md-offset-1 mx-auto'>
                    <h4 class="text-center">{{ trans('dashboard.disk_usage') }}</h4>
                    <div class="progress">
                        <div class="progress-bar progress-bar-primary" style="width: {{ 100 - $disk_space }}%">
                            {{ number_format(100 - $disk_space, 2) }}%
                            <span class="sr-only">35% Complete (success)</span>
                        </div>
                    </div>
                </div>

            </div>


            <div class='col-lg-4 col-sm-12 m-auto text-center py-5 mt-5'>
                <img src='{{ url($admin_logo) }}' class='img img-responsive img-rounded my-5' style='max-height:15rem;max-width:100%;' />
            </div>

            <div class='col m-auto '>


                @can('view', 'search')
                    <form class='form-inline mt-4 ' action="{{ route('search.show', ['search' => 'search']) }}" method='GET'>
                        @csrf
                        <div class='form-group'>
                            <div class="input-group">
                                <input type='text' pattern=".{3,}" title="Minimum 3 characters" class='form-control'
                                    name='search' id='exampleInputAmount' style='min-width:250px;'
                                    placeholder="{{ trans('dashboard.search_bar') }}" required>

                                <div class="input-group-prepend">
                                    <button type='submit' class='btn btn-link btn-sm border-0 p-0'>
                                        <span class='fa fa-search text-white' aria-hidden='true'></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                @endcan


                @if ($upgrade != null && $upgrade->isNewVersionAvailable())
                <div class="container">
                    <div class="alert alert-warning mt-4" role="alert">

                        <p class="fw-bold"> {{ trans('dashboard.update_available') . ' ' . $upgrade->getVersionAvailable() }}</p>
                        <p class="pt-1">{{ trans('dashboard.update_message') }}</p>

                        @can('update', 'upgrade')
                        <a href="{{ route('upgrade.index') }}" class='btn btn-primary w-100' >{{ trans('dashboard.update_now') }}</a>
                        @endcan

                    </div>
                </div>
                @endif

            </div>
        </div>

        <div class="mt-3 mb-4 p-2">
            <h1 class="text-center">Hello {{ auth()->user()->name }}!</h1>
        </div>

        <div class='container col-md-12'>
            <div class='row'>

                <div class='col-md-4 mb-2'>
                    <div class='panel panel-primary'>
                        <div class='panel-heading  bg-primary text-white p-2'>
                            <h6 class='panel-title mb-0 p-1'>
                                <b>{{ trans('dashboard.posted_news_count') }}</b>
                                <div class='float-end'><i class='fa fa-newspaper'></i></div>
                            </h6>
                        </div>
                        <div class='panel-body bg-dark text-center text-white'>
                            <h5 class="p-3">{{ $blogposts }}</h5>
                        </div>
                    </div>
                </div>


                <div class='col-md-4 mb-2'>
                    <div class='panel panel-primary'>
                        <div class='panel-heading bg-primary text-white p-2'>
                            <h6 class='panel-title mb-0 p-1'>
                                <b>{{ trans('dashboard.registered_users_count') }}</b>
                                <div class='float-end'><i class='fa fa-users'></i></div>
                            </h6>
                        </div>
                        <div class='panel-body bg-dark text-center text-white'>
                            <h5 class="p-3">{{ $users }}</h5>
                        </div>
                    </div>
                </div>

                <div class='col-md-4 mb-2'>
                    <div class='panel panel-primary'>
                        <div class='panel-heading  bg-primary text-white p-2'>
                            <h6 class='panel-title mb-0 p-1'>
                                <b>{{ trans('dashboard.visits_count') }}</b>
                                <div class='float-end'><i class='fa fa-binoculars'></i></div>
                            </h6>
                        </div>
                        <div class='panel-body bg-dark text-center text-white'>
                            <h5 class="p-3">{{ $visits }}</h5>
                        </div>
                    </div>
                </div>


            </div>


        </div>



    </div>
@endsection
