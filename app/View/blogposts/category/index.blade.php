@extends('layout', ['title' => trans('category.category')])

@section('content')
    <div class='container main-container'>
        <div class='row'>
            <div class="col-md-12">

                <div class="card mb-3">

                    @include('breadcrumb', [
                        'links' => [['name' => trans('dashboard.content')], ['name' => 'Blog', 'url' => route('blogpost.index')]],
                        'page_title' => trans('category.category'),
                        'stats' => [['label' => trans('blogpost.all'), 'value' => $all_category->count()]],
                    ])

                    <div class='card-body'>
                        @can('create', 'blogpostcategory')
                        <form action="{{ route('blogpostcategory.store') }}" class='form-inline pull-right my-3' role='form'
                            method='POST'>

                            @csrf

                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <label for="inputPassword6"
                                        class="col-form-label">{{ trans('category.add_category') }}</label>
                                </div>
                                <div class="col-auto">
                                    <input type='text' class='form-control' id='cat' name='name'
                                        placeholder='Enter new category' required>
                                </div>
                                <div class="col-auto">
                                    <span class="form-text">
                                        <button type='submit' class='btn btn-primary'>{{ trans('actions.add') }}</button>
                                    </span>
                                </div>
                            </div>

                        </form>
                        @endcan


                        <table class='table table-hover'>
                            <thead>
                                <tr class="bg-dark text-white">
                                    <th class="col-1">{{ trans('category.th_id') }}</th>
                                    <!--<th>{{ trans('category.th_image') }}</th>-->
                                    <th class="col">{{ trans('category.th_category') }}</th>
                                    <th class="col">{{ trans('category.th_posts') }}</th>
                                    <th class="col-5 text-center">{{ trans('actions.th_action') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($all_category as $each)
                                    <tr>
                                        <td class="col">{{ $each->id }}</td>

                                        <td class='col'>
                                            <a
                                                href="{{ route('blogpostcategory.show', ['blogpostcategory' => $each]) }}">{{ $each->name }}</a>
                                        </td>

                                        <td class="col">
                                            <span class='badge rounded-pill bg-dark'>{{ $each->blogposts->count() }}</span>
                                        </td>

                                        <td class="col  text-center">


                                            <div class="dropdown">
                                                <div data-bs-toggle="dropdown" aria-expanded="false"
                                                    style="cursor:pointer;">
                                                    <i class="fa-solid fa-ellipsis-vertical text-dark fs-5"></i>
                                                </div>
                                                <ul class="dropdown-menu text-dark bg-white">
                                                    @can('update', 'blogpostcategory')
                                                    <li>
                                                        <a href="{{ route('blogpostcategory.edit', ['blogpostcategory' => $each]) }}"
                                                            class="dropdown-item text-decoration-none text-dark">
                                                            <i class="fa fa-pencil me-2" aria-hidden="true"></i>
                                                            {{ trans('actions.edit') }}
                                                        </a>
                                                    </li>
                                                    @endcan
                                                    @can('delete', 'blogpostcategory')
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
                                @endforeach

                            </tbody>
                        </table>

                        @can('delete', 'blogpostcategory')
                        @foreach ($all_category as $each)
                            @include('confirm_delete', [
                                'route' => route('blogpostcategory.destroy', [
                                    'blogpostcategory' => $each,
                                ]),
                                'id' => 'delete_' . $each->id,
                                'header' => trans('actions.are_you_sure'),
                                'name' => $each->name,
                                'content_type' => 'category',
                                'delete_text' => trans('actions.delete'),
                                'cancel' => trans('actions.cancel'),
                            ])
                        @endforeach
                        @endcan
                    </div>

                </div>
            </div>
        </div>
    @endsection
