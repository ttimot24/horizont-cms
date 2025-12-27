@extends('layout', ['title' => trans('user.view_user')])

@section('content')
    <div class='container main-container'>


        <div class="card mb-3">
            @php

                $buttons = [];

                if ($previous_user) {
                    array_push($buttons, [
                        'icon' => 'fa-angle-left',
                        'label' => trans('actions.previous'),
                        'class' => 'btn-dark rounded btn-sm',
                        'route' => route('user.show', ['user' => $previous_user]),
                    ]);
                }

                if ($next_user) {
                    array_push($buttons, [
                        'icon' => 'fa-angle-right',
                        'label' => trans('actions.next'),
                        'class' => 'btn-dark rounded btn-sm',
                        'route' => route('user.show', ['user' => $next_user]),
                    ]);
                }

            @endphp

            @include('breadcrumb', [
                'links' => [['name' => trans('dashboard.content')], ['name' => trans('user.users'), 'url' => route('user.index')]],
                'page_title' => trans('user.view_user'),
                'buttons' => null,
                'buttons_right' => $buttons,
            ])

            <div class="card-body">

                <section class='row'>
                    <div class='col-md-4 text-center' valign='top'>
                        <div class="card">
                            <div class="card-body p-1">
                                <a class='btn btn-link' data-bs-toggle='modal' data-bs-target='#modal-xl-{{ $user->id }}'>
                                    <img src='{{ $user->getImage() }}' class='img img-thumbnail mt-3'>
                                </a>

                                <div class="my-3">
                                    <div class='btn-group' role='group'>
                                        @can('update', 'user')
                                        <a href='#' type='button' class='btn btn-warning me-1'><span
                                                class='fa fa-star' aria-hidden='true'></span>
                                            {{ trans('actions.deactivate') }}</a>
                                        @endcan
                                        @can('update', 'user')
                                        <a href="{{ route('user.edit', ['user' => $user]) }}" type='button'
                                            class='btn btn-warning'><span class='fa fa-pencil' aria-hidden='true'></span>
                                            {{ trans('actions.edit') }}</a>
                                        @endcan
                                    </div>
                                    @can('delete', 'user')
                                    @if ($user->role_id < \Auth::user()->role_id && !$user->is(Auth::user()))
                                        <button type='button' class='btn btn-danger' data-bs-toggle='modal'
                                            data-bs-target='#delete_{{ $user->id }}'>
                                            <span class='fa fa-trash' aria-hidden='true'></span>
                                            {{ trans('actions.remove') }}
                                        </button>
                                    @endif
                                    @endcan
                                </div>

                                <b class="d-block text-center mb-3">{{ trans('user.view_full_name') }} : <a
                                        class="color-primary">{{ $user->name }}</a></b>
                                <b class="d-block text-center mb-3">{{ trans('user.view_user_name') }} : <a
                                        class="color-primary">{{ $user->username }}</a></b>
                                <b class="d-block text-center mb-3">{{ trans('user.view_rank') }} :
                                    <a>{{ $user->role->name }}</a></b>
                                <b class="d-block text-center mb-3">{{ trans('user.view_email') }} : <a
                                        class="color-primary">{{ $user->email }}</a></b>
                                <b class="d-block text-center mb-3">{{ trans('user.view_phone') }} : <a
                                        class="color-primary">{{ $user->phone }}</a></b>
                                <b class="d-block text-center mb-3">{{ trans('user.view_registered_on') }} : </br><a
                                        class="color-primary">{{ $user->created_at->format(\Settings::get('date_format', \Config::get('horizontcms.default_date_format'), true)) }}</a></b>
                                <b class="d-block text-center mb-3">{{ trans('user.view_logins') }} : <a
                                        class="color-primary">{{ $user->visits }}</a></b>
                            </div>
                        </div>

                    </div>

                    <div valign='top' class='col-md-8'>

                        @can('update', 'user')
                        @if (!$user->isActive())
                            <div class='card mb-3'>
                                <div class='card-header bg-danger'>
                                    <h4 class='card-title mt-2 mb-2 pt-0 pb-0'><b>Inactive user</b></h4>
                                </div>
                                <div class='card-body' style='text-align:center;'>
                                    <font size='4'>

                                        {{ trans('user.inactive_about', ['day_count' => $user->created_at->diffForHumans()]) }}

                                    </font> 
                                    
                                    
                                    <form method="POST" action="{{ route('user.update', ['user' => $user]) }}" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="active" value="1">
                                        <button type="submit" class="btn btn-sm btn-danger pull-right">
                                            {{ trans('Force activate') }}
                                        </button>
                                    </form>
                                    
                                </div>
                            </div>
                        @endif
                        @endcan

                        @can('view', 'blogpost')
                        @if ($user->isAdmin())
                            <div class="card">
                                <div class="card-header">
                                    <h3>{{ trans('blogpost.blogposts') }} ({{ $user->blogposts->count() }})</h3>
                                </div>
                                <div class="card-body">
                                    <table class='table table-condensed table-hover'>
                                        <thead>
                                            <tr class='bg-dark text-white'>
                                                <th class='col-4'>Image</th>
                                                <th class='col-6'>Title</th>
                                                <th class='col-2'>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($user->blogposts->reverse() as $each)
                                                <tr>
                                                    <td class='col-4'>
                                                        <a href="{{ route('blogpost.show', ['blogpost' => $each]) }}">
                                                            <img src="{{ $each->getThumb() }}"
                                                                class='img img-thumbnail bg-dark', width='250'
                                                                style='object-fit:cover;height:150px;' />
                                                        </a>
                                                    </td>
                                                    <td class='col-6'>
                                                        <a href="{{ route('blogpost.show', ['blogpost' => $each]) }}">
                                                            <h5>{{ $each->title }}</h5>
                                                        </a>
                                                        <p>{{ $each->summary }}
                                                        </p>
                                                    </td>

                                                    <td class='col-2 text-right'>
                                                        {{ $each->created_at->format('Y.m.d') }}</br>
                                                        <font size='2'><i>at</i>
                                                            {{ $each->created_at->format('H:i:s') }}</font>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                    </div>
                </section>

                </br></br>
                @endif
                @endcan

                @include('image_details', ['modal_id' => $user->id, 'image' => $user->getImageFilePath()])

                @can('delete', 'user')
                @include('confirm_delete', [
                    'route' => route('user.destroy', ['user' => $user]),
                    'id' => 'delete_' . $user->id,
                    'header' => trans('actions.are_you_sure'),
                    'name' => $user->username,
                    'content_type' => 'user',
                    'delete_text' => trans('actions.delete'),
                    'cancel' => trans('actions.cancel'),
                ])
                @endcan

                @can('view', 'blogpostcomment')
                <div class="card">

                    <div class="card-header">
                        <h3>{{ trans('blogpost.comments') }} ({{ $user->comments->count() }})</h3>
                    </div>

                    <div class="card-body">

                        <table class='table table-condensed table-hover'>
                            <thead>
                                <tr class="bg-dark text-white">
                                    <th class="col-3">{{ trans('blogpost.post') }}</th>
                                    <th class="col-8">{{ trans('blogpost.th_comment') }}</th>
                                    <th class="col-1">{{ trans('blogpost.th_date') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($user->comments as $each)
                                    @if ($each->blogpost != null)
                                        <tr>
                                            <td class='col-3'>
                                                <a href="{{ route('blogpost.show', ['blogpost' => $each->blogpost]) }}">{{ $each->blogpost->title }}</a>
                                            </td>
                                            <td class='col-8' style='text-align:justify;'>{{ $each->comment }}</td>

                                            <td class='col-1'>{{ $each->created_at->format('Y.m.d') }}</br>
                                                <div class="fs-6"><i>at</i> {{ $each->created_at->format('H:i:s') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                </div>
                @endcan
            </div>

        </div>
        </section>

    </div>
@endsection
