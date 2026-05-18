@extends('layout', ['title' => trans('search.title')])

@section('content')
    <div class='container main-container'>

    <div class="card pb-5">

            @include('breadcrumb', [
                'links' => [['name' => trans('dashboard.content')], ['name' => trans('Dashboard'), 'url' => route('dashboard.index')]],
                'page_title' => trans('search.title'),
            ])

    <div class="container p-4">
        <div class="row">

            <h2 class='col-md-8'>{!! trans('search.found_matches', ['quantity' => $search_engine->getTotalCount(), 'search_word' => $search_for]) !!}</h2>

            <div class='col-md-4 col-sm-12 my-auto pt-3'>
                <form class='form-inline' action="{{ route('search.show', ['search' => 'search']) }}" method="GET">
                    @csrf
                    <div class='form-group'>
                        <div class='input-group'>
                            <input type='text' pattern=".{3,}" title="Minimum 3 characters" class='form-control'
                                name='search' id='exampleInputAmount' placeholder="{{ trans('dashboard.search_bar') }}"
                                required>
                                <div class="input-group-text bg-dark">
                                    <button type='submit' class='btn text-center btn-link p-0'>
                                        <span class='fa fa-search text-white' aria-hidden='true'></span>
                                    </button>
                                </div>
                        </div>
                    </div>
                    <!-- <button type='submit' class='btn btn-primary'>Search</button>-->
                </form>

            </div>
        </div>

        @can('view', 'blogpost')
        @if(count($search_engine->getResultsFor(\App\Model\Blogpost::class)) > 0)
        <div class="mt-5">
            <div class='container'>
                <h3>{{ trans('blogpost.blogposts') }}({{ count($search_engine->getResultsFor(\App\Model\Blogpost::class)) }})</h3>
                <table class='table table-hover'>
                    <thead class="bg-dark text-white">
                        <th>Id</th>
                        <th>Title</th>
                        <th>Created At</th>
                    </thead>
                    <tbody>
                @foreach ($search_engine->getResultsFor(\App\Model\Blogpost::class) as $each)
                <tr>
                    <td>{{ $each->id }}</td>
                    <td><a href="{{ route('blogpost.show', ['blogpost' => $each]) }}">{{ $each->title }}</a></td>
                    <td>{{ $each->created_at }}</td>
                </tr>
                @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        @endcan


        @can('view', 'user')
        @if(count($search_engine->getResultsFor(\App\Model\User::class)) > 0)
        <div class="mt-5">
            <div class='container'>
                <h3>{{ trans('user.users') }} ({{ count($search_engine->getResultsFor(\App\Model\User::class)) }})</h3>
                <table class='table table-hover'>
                    <thead class="bg-dark text-white">
                        <th>Id</th>
                        <th>Name</th>
                        <th>Created At</th>
                    </thead>
                    <tbody>
                @foreach ($search_engine->getResultsFor(\App\Model\User::class) as $each)
                <tr>
                    <td>{{ $each->id }}</td>
                    <td><a href="{{ route('user.show', ['user' => $each]) }}">{{ $each->name }}</a></td>
                    <td>{{ $each->created_at }}</td>
                </tr>
                @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        @endcan


        @can('view', 'page')
        @if(count($search_engine->getResultsFor(\App\Model\Page::class)) > 0)
        <div class="mt-5">
            <div class='container'>
                <h3>{{ trans('page.pages') }} ({{ count($search_engine->getResultsFor(\App\Model\Page::class)) }})</h3>
                <table class='table table-hover'>
                    <thead class="bg-dark text-white">
                        <th>Id</th>
                        <th>Name</th>
                        <th>Created At</th>
                    </thead>
                    <tbody>
                @foreach ($search_engine->getResultsFor(\App\Model\Page::class) as $each)
                <tr>
                    <td>{{ $each->id }}</td>
                    <td><a href="{{ route('page.show', ['page' => $each]) }}">{{ $each->name }}</a></td>
                    <td>{{ $each->created_at }}</td>
                </tr>
                @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        @endcan



        @can('view', 'media')
            <h3>{{ trans('file.files') }} ({{ count($files) }})</h3>
            <div class='container'>
            </div>
        @endcan


        </div>

        </div>
        </div>
    @endsection
