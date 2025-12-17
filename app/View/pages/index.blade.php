@extends('layout', ['title' => trans('page.pages') ])

@section('content')
    <div class='container main-container'>

        <div class="card mb-3">

            @include('breadcrumb', [
                'links' => [['name' => trans('dashboard.content')], ['name' => trans('page.pages'), 'url' => route('page.index')]],
                'page_title' => trans('page.pages'),
                'stats' => [['label' => trans('page.all'), 'value' => $all_pages->total()],
                ['label' => trans('page.visible'), 'value' => $visible_pages],
                ['label' => trans('page.invisible'), 'value' => $all_pages->total() - $visible_pages]],
                'buttons' => [
                   [
                        'label' => trans('page.create_page_button'),
                        'route' => route('page.create'),
                        'class' => 'btn-info',
                    ],
                ],
                'buttons_right' => [
                    [
                        'icon' => 'fa-arrows-v',
                        'label' => trans('page.order'),
                        'class' => 'btn-default',
                        'data' => "id=orderer onclick=dragndroporder();  data-csrf=".csrf_token(),
                          
                    ],
                ],
            ])


            <div class="card-body">

                <table id="page-list-table" class='table table-hover table-condensed'>
                    <thead>
                        <tr class="bg-dark text-white">
                            <th>{{ trans('page.th_id') }}</th>
                            <th>{{ trans('page.th_image') }}</th>
                            <th>{{ trans('page.th_name') }}</th>
                            <th>{{ trans('page.th_template') }}</th>
                            <th>{{ trans('page.th_visibility') }}</th>
                            <th>{{ trans('page.th_type') }}</th>
                            <th>{{ trans('page.th_child_links') }}</th>
                            <th class="text-center">{{ trans('actions.th_action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="pages">



                        @foreach ($all_pages as $each)
                            <tr>
                                <td>{{ $each->id }}

                                    @if ($each->is($home_page))
                                        <i class='fa fa-home' style='font-size:20px;'></i>
                                    @else
                                    @can('update', 'page')
                                        <a href='admin/#' data-bs-toggle='modal'
                                            data-bs-target='.mo-{{ $each->id }}'><i class='fa fa-home' id='hidden-home'
                                                style='font-size:20px;'></i></a>
                                    @endcan
                                    @endif

                                    <br><span class='badge bg-secondary'>{{ strtoupper($each->language) }} </span>


                                </td>
                                <td>
                                @if($each->getFeaturedMediaType()==='video')
                                    <video controls="false" width=70 height=50 >
                                        <source src="{{ $each->getImage()}}">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img src='{{ $each->getThumb() }}' class='img img-rounded' style='object-fit:cover;' width=70 height=50 /> 
                                @endif
                                </td>
                                <td>{{ $each->name }}
                                </td>
                                <td>@if($each->url)<span class='badge bg-dark w-75'> {{$each->url}}</span> @endif</td>
                                <td>

                                    @if ($each->visibility == 1)
                                        <span class='badge bg-success w-75'>{{ trans('page.visible') }}</span>
                                    @else
                                        <span class='badge bg-danger w-75'>{{ trans('page.invisible') }}</span>
                                    @endif

                                </td>
                                <td>

                                    @if ($each->parent == null)
                                        <b>{{ trans('page.menu_type1') }}</b>
                                    @else
                                        <span class='badge bg-info w-75'>
                                            {!! trans('page.menu_type2', ['parent_menu' => $each->parent->name]) !!}
                                        </span>
                                    @endif

                                </td>



                                <td class='ps-4'><span class='badge rounded-pill bg-dark'>{{ $each->subpages->count() }}
                                    </span></td>


                                <td class='text-center'>

                                    <div class="dropdown">
                                        <div data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                                            <i class="fa-solid fa-ellipsis-vertical text-dark fs-5"></i>
                                        </div>
                                        <ul class="dropdown-menu text-dark">
                                            <li>
                                                <a target="_blank" href="{{ url(config('theme::theme.content.page.preview.url', 'pages').'/'.$each->getSlug()) }}" type='button'
                                                    class='dropdown-item text-decoration-none text-dark'>
                                                    <span class='fa fa-eye me-2' aria-hidden='true'></span>
                                                    {{ trans('View') }}
                                                </a>
                                            </li>
                                            @can('update', 'page')
                                            <li>
                                                <a href="{{ route('page.edit', ['page' => $each]) }}"
                                                    class="dropdown-item text-decoration-none text-dark">
                                                    <i class="fa fa-pencil me-2" aria-hidden="true"></i>
                                                    {{ trans('actions.edit') }}
                                                </a>
                                            </li>
                                            @endcan
                                            @can('delete', 'page')
                                            <li>
                                                <a data-bs-toggle='modal' data-bs-target=#delete_<?= $each->id ?>
                                                    class="dropdown-item text-danger text-decoration-none"
                                                    style="cursor: pointer;">
                                                    <i class="fa fa-trash me-2" aria-hidden="true"></i>
                                                    {{ trans('actions.delete') }}
                                                </a>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>

                                </td>
                            </tr>


                            <div class="modal mo-{{ $each->id }}" id="mo-{{ $each->id }}" tabindex="-1"
                                role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-warning bg-warning text-white">
                                            <h4 class="modal-title" id="myModalLabel">{{ trans('page.change_homepage') }}
                                            </h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            {{ trans('page.are_you_sure_to_set', ['page_name' => $each->name]) }}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default"
                                                data-bs-dismiss="modal">{{ trans('actions.close') }}
                                            </button>
                                            <a href="admin/page/set-home-page/{{ $each->id }}" type="button"
                                                class="btn btn-primary">{{ trans('actions.set') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </td>
                            </tr>
                        @endforeach


                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $all_pages->links() }}
                </div>

            </div>

            @can('delete', 'page')
            @foreach ($all_pages as $each)
                @include('confirm_delete', [
                    'route' => route('page.destroy', ['page' => $each]),
                    'id' => 'delete_' . $each->id,
                    'header' => trans('actions.are_you_sure'),
                    'name' => $each->title,
                    'content_type' => 'page',
                    'delete_text' => trans('actions.delete'),
                    'cancel' => trans('actions.cancel'),
                ])
            @endforeach
            @endif

        </div>
    @endsection
