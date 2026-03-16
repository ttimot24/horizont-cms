@extends('layout', ['title' => trans('blogpost.view_blogpost')])

@section('content')
    <div class='container main-container'>

        <div class="card mb-3">

                @php
                    
                    $buttons = [];
                    
                    if ($previous_blogpost) {
                        array_push($buttons, [
                            'icon' => 'fa-angle-left',
                            'label' => trans('actions.previous'),
                            'class' => 'btn-dark rounded btn-sm',
                            'route' => route('blogpost.show', ['blogpost' => $previous_blogpost]),
                        ]);
                    }
                    
                    if ($next_blogpost) {
                        array_push($buttons, [
                            'icon' => 'fa-angle-right',
                            'label' => trans('actions.next'),
                            'class' => 'btn-dark rounded btn-sm',
                            'route' => route('blogpost.show', ['blogpost' => $next_blogpost]),
                        ]);
                    }
                    
                @endphp

                @include('breadcrumb', [
                    'links' => [['name' => trans('dashboard.content')], ['name' => 'Blog', 'url' => route('blogpost.index')], ['name' => $blogpost->title, 'url' => route('blogpost.show', ['blogpost' => $blogpost])], ['name' => 'View']],
                    'page_title' => trans('blogpost.view_blogpost'),
                    'buttons' => null,
                    'buttons_right' => $buttons,
                ])

                <div class="card-body">

                    <section class='row'>
                        <div class='col-md-4'>
                        <div class="card">
                            <button type='button' class='btn btn-link w-100' data-bs-toggle='modal'
                                data-bs-target='#modal-xl-{{ $blogpost->id }}'>

                                @if($blogpost->getFeaturedMediaType()==='video')
                                    <video controls class="w-100" style="max-height:500px;">
                                        <source src="{{ $blogpost->getImage()}}">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img src='{{ $blogpost->getImage() }}' width='350' class='img img-thumbnail mt-3' />
                                @endif
                            </button>

                            <div class="text-center">
                                <div class='btn-group my-3' role='group'>
                                    @if (!$blogpost->isFeatured())
                                        <form method="POST"
                                            action="{{ route('blogpost.update', ['blogpost' => $blogpost]) }}">
                                            @csrf
                                            @method('PUT')

                                            <input type="hidden" name="active" value="2">
                                            <button type="submit" class='btn btn-success'>
                                                <span class='fa fa-star' aria-hidden='true'></span>
                                                {{ trans('blogpost.primary') }}
                                            </button>

                                        </form>
                                    @else
                                        <form method="POST"
                                            action="{{ route('blogpost.update', ['blogpost' => $blogpost]) }}">
                                            @csrf
                                            @method('PUT')

                                            <input type="hidden" name="active" value="1">
                                            <button type="submit" class='btn btn-info'>
                                                <span class='fa fa-minus' aria-hidden='true'></span>
                                                {{ trans('Revoke') }}
                                            </button>

                                        </form>
                                    @endif
                                                      <a target="_blank" href="{{ url(config('theme::theme.content.blogpost.preview.url', 'blogposts').'/'.$blogpost->getSlug()) }}" type='button'
                                        class='btn btn-secondary text-white'>
                                        <span class='fa fa-eye me-2' aria-hidden='true'></span>
                                        {{ trans($blogpost->isDraft()? 'Preview': 'View') }}
                                </a>
                                    @can('update', 'blogpost')
                                    <a href="{{ route('blogpost.edit', ['blogpost' => $blogpost]) }}" type='button'
                                        class='btn btn-warning'><i class='fa fa-pencil'
                                            aria-hidden='true'></i>
                                        {{ trans('actions.edit') }} </a>
                                    @endcan

                                    @can('delete', 'blogpost')
                                    <button type='button' class='btn btn-danger' data-bs-toggle='modal'
                                        data-bs-target='#delete_{{ $blogpost->id }}'>
                                        <i class='fa fa-trash' aria-hidden='true'></i>
                                        {{ trans('actions.remove') }}
                                    </button>
                                    @endcan
                                </div>

                                @if ($blogpost->isDraft())
                                    <div class="row m-3 d-flex justify-content-center">
                                        <span class="badge bg-info text-white w-50">
                                            {{ trans('actions.draft') }}
                                        </span>
                                    </div>
                                @endif        

                                @if ($blogpost->author)
                                    <b class="d-block mb-3">{{ trans('blogpost.author') }} : <a
                                            href="{{ route('user.show', ['user' => $blogpost->author]) }}">{{ $blogpost->author->username }}</a></b>
                                @else
                                    <b class="d-block mb-3">{{ trans('blogpost.author') }} : <a class="color-primary">
                                            {{ trans('blogpost.removed_user') }} </a> </b>
                                @endif
                                <b class="d-block mb-3">{{ trans('settings.adminarea_language') }} :  <span class='badge bg-secondary'>{{ strtoupper($blogpost->language) }} </span></b>
                                <b class="d-block mb-3">{{ trans('blogpost.slug') }} : <a
                                        class="color-primary">{{ $blogpost->getSlug() }}</a></b>
                                <b class="d-block mb-3">{{ trans('blogpost.published_on') }} : <a
                                        class="color-primary">{{ $blogpost->created_at->format(\Settings::get('date_format', \Config::get('horizontcms.default_date_format'), true)) }}</a></b>
                                
                                @if($blogpost->updated_at)
                                <b class="d-block mb-3">{{ trans('blogpost.last_updated_at') }} : <a
                                            class="color-primary">{{ $blogpost->updated_at->format(\Settings::get('date_format', \Config::get('horizontcms.default_date_format'), true)) }}</a></b>
                                @endif

                                <b class="d-block mb-3">{{ trans('blogpost.category') }} : 
                                    @foreach ($blogpost->categories as $category)      
                                        <a class="color-primary me-3"
                                            href="{{ route('blogpostcategory.show', ['blogpostcategory' => $category]) }}">
                                            {{ $category->name }}
                                        </a>
                                    @endforeach
                                </b>
                                <b class="d-block mb-3">{{ trans('blogpost.reading_time') }} : <a
                                        class="color-primary">{{ ceil($blogpost->getReadingTime() / 60) }} mins</a></b>
                                <b class="d-block mb-3">{{ trans('blogpost.characters') }} : <a
                                        class="color-primary">{{ $blogpost->getTotalCharacterCount() }}</a></b>
                                <b class="d-block mb-3">{{ trans('blogpost.words') }} : <a
                                        class="color-primary">{{ $blogpost->getTotalWordCount() }}</a></b>
                                <b class="d-block mb-3">{{ trans('blogpost.comments') }} : <a
                                        class="color-primary">{{ $blogpost->comments->count() }}</a></b>
                            </div>
                        </div>
                        </div>

                        <div class="col-md-8">
                            <div class='card bg-dark text-white p-4 overflow-auto'>
                                <h3 class="fw-bold">{{ $blogpost->title }}</h3>
                                <hr />
                                <b>{{ $blogpost->summary }}</b>
                                <p class="pt-4">
                                    {!! $blogpost->text !!}
                                </p>
                            </div>
                        </div>
                    </section>
                    <div id='comments'></div>



                    @include('image_details', [
                        'modal_id' => $blogpost->id,
                        'image' => $blogpost->getImageFilePath(),
                    ])

                    @can('delete', 'blogpost')
                    @include('confirm_delete', [
                        'route' => route('blogpost.destroy', ['blogpost' => $blogpost]),
                        'id' => 'delete_' . $blogpost->id,
                        'header' => trans('actions.are_you_sure'),
                        'name' => $blogpost->title,
                        'content_type' => 'post',
                        'delete_text' => trans('actions.delete'),
                        'cancel' => trans('actions.cancel'),
                    ])
                    @endcan

                    @can('view', 'blogpostcomment')
                    @include('blogposts.comments', ['user' => \Auth::user()])
                    @endcan


                </div>

        </div>
    @endsection
