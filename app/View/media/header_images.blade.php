@extends('layout', ['title' => trans('Header Images')])

@section('content')
    <div class='container main-container'>

        <div class="card mb-3">

            @include('breadcrumb', [
                'links' => [['name' => trans('dashboard.content')], ['name' => trans('Media')]],
                'page_title' => trans('Header images'),
                'stats' => [
                    ['label' => trans('user.all'), 'value' => $slider_images->count()+$slider_disabled->count()],
                    ['label' => trans('Slider'), 'value' => $slider_images->count()]
                ],
                'buttons_right' => [
                    [
                        'icon' => 'fa-upload',
                        'label' => 'Upload images',
                        'class' => 'btn-primary',
                        'data' => 'data-bs-toggle=modal data-bs-target=.upload_images'
                    ]
                ],
            ])

            <div class="card-body">

                <div class="card shadow-sm rounded-1 border-0 bg-info text-white p-5 mb-5">
                    <h4>Currently on the slider:</h4>

                    <div class="row">

                        @if ($slider_images->count() > 0)
                            @foreach ($slider_images as $each)
                                <div class="card col-md-3 pt-3">
                                    <a class='d-flex justify-content-end me-2 mb-4' data-bs-toggle='modal'
                                        data-bs-target='#headline-image-{{ $each->id }}'>
                                        <span class='fa fa-pencil' aria-hidden='true'
                                            style='font-size: 1.4em;z-index:15;top:3px;right:3px;margin-bottom:-15px;cursor:pointer'></span>
                                    </a>

                                    @if($each->getFeaturedMediaType()==='video')
                                        <video controls width='100%' height='75%;' style="object-fit:cover;">
                                            <source src="{{ $each->getImage() }}" >
                                            Your browser does not support the video tag.
                                        </video> 
                                    @else
                                    <img src='{{ $each->getImage() }}' alt=''
                                        class='card-img-top' width='100%' height='75%;' style="object-fit:cover;">
                                    @endif

                                    <div class="card-body text-black">
                                        <h5 class="card-title">{{ $each->title }}<small> | {{$each->type}}</small></h5>
                                    </div>
                                    <ul class="list-group list-group-flush mb-3 pt-3">
                                        <form action="{{ route('headerimage.update', ['headerimage' => $each]) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="active" value="0">
                                            <button type="submit" class="btn btn-danger btn-xs w-100">
                                                Remove from slider
                                            </button>
                                        </form>
                                    </ul>
                                </div>


                                <div class='modal edit_images' id='headline-image-{{ $each->id }}' tabindex='-1'
                                    role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog modal-xl'>
                                        <div class='modal-content'>
                                            <div class='modal-header modal-header-primary bg-primary'>
                                                <h4 class='modal-title text-white'>Edit headline</h4>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                                    aria-label='Close'></button>
                                            </div>
                                            <form action='{{ route('headerimage.update', ['headerimage' => $each]) }}'
                                            method='POST'>
                                            <div class='modal-body text-dark'>
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <label for="header-image-title" class="form-label">Tagline</label>
                                                    <input type="text" class="form-control" name="title"
                                                        id="header-image-title" value="{{ $each->title }}">
                                                    <input type="hidden" name="image" value="{{ $each->image }}">
                                                </div>
                                                <div class="mb-3">
                                                    <div class="row">
                                                        <div class="col-11">
                                                            <label for="header-image-image"
                                                                class="form-label">Image</label>
                                                            <input type="text" class="form-control disabled"
                                                                id="header-image-title" value="{{ $each->getImage() }}"
                                                                disabled>

                                                        </div>
                                                        <div class="col-1">
                                                            <label for="header-image-image"
                                                                class="form-label">Show</label><br>
                                                            <a href="{{ $each->getImage() }}" target="_blank"
                                                                class="btn btn-default"><i class="fa fa-external-link"
                                                                    aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="header-image-title" class="form-label">Link</label>
                                                    <input type="text" class="form-control" name="link"
                                                        id="header-image-title" value="{{ $each->link }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="exampleFormControlTextarea1"
                                                        class="form-label">Summary</label>
                                                    <textarea class="form-control" name="description" id="exampleFormControlTextarea1" rows="5">{{ $each->description }}</textarea>
                                                </div>
                                                <input type="hidden" name="active" value="{{ $each->active }}">
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <b>Created at: </b> {{ $each->created_at }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <b>Created by: </b> <a
                                                                    href="{{ route('user.show', ['user' => $each->author]) }}">{{ $each->author->username }}</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <b>Updated at: </b> {{ $each->updated_at }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='modal-footer'>
                                                <button type='submit'
                                                    class='btn btn-primary'>{{ trans('actions.save') }}</button>
                                                <button type='button' class='btn btn-default'
                                                    data-bs-dismiss='modal'>{{ trans('actions.cancel') }}</button>
                                            </div>
                                        </form>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            @endforeach
                        @else
                            <h4 class='text-center'><br>No images on the slider!<br></h4>
                        @endif
                    </div>

                </div>
                <hr>
                <div class='container'>
                    <h3>Available images:</h3>
                    <div class="row">
                        @foreach ($slider_disabled as $each)
                            <div class="card col-md-3">
                                <div class="card-header py-2 px-0 bg-white">
                                    <div class="row">
                                        <div class="col-9">
                                            <form action="{{ route('headerimage.update', ['headerimage' => $each]) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="active" value="1">
                                                <button type="submit" class="btn  btn-link p-0">
                                                    Add to slider
                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-1">
                                            <a href="#" data-bs-toggle='modal'
                                                data-bs-target='#headline-image-{{ $each->id }}'>
                                                <span class='fa fa-pencil' aria-hidden='true'
                                                    style='font-size: 1.4em;z-index:15;'></span>
                                            </a>
                                        </div>
                                        @can('delete', 'headerimage')
                                        <div class="col-2">
                                            <form action="{{ route('headerimage.destroy', ['headerimage' => $each]) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link p-0">
                                                    <span class='fa fa-trash' aria-hidden='true'
                                                        style='font-size: 1.4em;z-index:15;'></span>
                                                </button>
                                            </form>
                                        </div>
                                        @endcan
                                    </div>
                                </div>
                                @if($each->getFeaturedMediaType()==='video')
                                <video controls width='100%' height='75%;' style="object-fit:cover;">
                                    <source src="{{ $each->getImage() }}" >
                                    Your browser does not support the video tag.
                                </video> 
                                @else
                                <img src='{{ $each->getImage() }}' alt=''
                                    class='card-img-top' width='100%' height='75%;' style="object-fit:cover;">
                                @endif
                                <div class="card-body text-black">
                                    <h5 class="card-title">{{ $each->title }}<small> | {{$each->type}}</small></h5>
                                </div>
                            </div>


                            <div class='modal edit_images' id='headline-image-{{ $each->id }}' tabindex='-1'
                                role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                                <div class='modal-dialog modal-xl'>
                                    <div class='modal-content'>
                                        <div class='modal-header modal-header-primary bg-primary'>
                                            <h4 class='modal-title text-white'>Edit headline</h4>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal'
                                                aria-label='Close'></button>
                                        </div>
                                        <form action='{{ route('headerimage.update', ['headerimage' => $each]) }}'
                                            method='POST'>
                                            <div class='modal-body'>
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <label for="header-image-title" class="form-label">Tagline</label>
                                                    <input type="text" class="form-control" name="title"
                                                        id="header-image-title" value="{{ $each->title }}">
                                                    <input type="hidden" name="image" value="{{ $each->image }}">
                                                </div>
                                                <div class="mb-3">
                                                    <div class="row">
                                                        <div class="col-9">
                                                            <label for="header-image-image"
                                                                class="form-label">Media</label>
                                                            <input type="text" class="form-control disabled"
                                                                id="header-image-title" value="{{ $each->getImage() }}"
                                                                disabled>

                                                        </div>
                                                        <div class="col-2">
                                                            <label for="header-image-image"
                                                                class="form-label">Type</label>
                                                            <input type="text" class="form-control disabled"
                                                                id="header-image-type" value="{{ $each->type }}"
                                                                disabled>

                                                        </div>
                                                        <div class="col-1">
                                                            <label for="header-image-image"
                                                                class="form-label">Show</label><br>
                                                            <a href="{{ $each->getImage() }}" target="_blank"
                                                                class="btn btn-default"><i class="fa fa-external-link"
                                                                    aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="header-image-title" class="form-label">Link</label>
                                                    <input type="text" class="form-control" name="link"
                                                        id="header-image-title" value="{{ $each->link }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="exampleFormControlTextarea1"
                                                        class="form-label">Summary</label>
                                                    <textarea class="form-control" name="description" id="exampleFormControlTextarea1" rows="5">{{ $each->description }}</textarea>
                                                </div>
                                                <input type="hidden" name="active" value="{{ $each->active }}">
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <b>Created at: </b> {{ $each->created_at }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <b>Created by: </b> <a
                                                                    href="{{ route('user.show', ['user' => $each->author]) }}">{{ $each->author->username }}</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <b>Updated at: </b> {{ $each->updated_at }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='modal-footer'>
                                                <button type='submit'
                                                    class='btn btn-primary'>{{ trans('actions.save') }}</button>
                                                <button type='button' class='btn btn-default'
                                                    data-bs-dismiss='modal'>{{ trans('actions.cancel') }}</button>
                                            </div>
                                        </form>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                        @endforeach
                    </div>
                </div>

            </div>


            <div class='modal upload_images' id='create_file' tabindex='-1' role='dialog'
                aria-labelledby='myModalLabel' aria-hidden='true'>
                <div class='modal-dialog modal-xl p-5'>
                    <div class='modal-content'>
                        <div class='modal-header modal-header-primary bg-primary'>
                            <h4 class='modal-title text-white'>Upload images</h4>
                            <button type='button' class='btn-close' data-bs-dismiss='modal'
                                aria-label='Close'></button>
                        </div>
                        <form action="{{ route('headerimage.store') }}" method='POST' enctype='multipart/form-data'>
                            <div class='modal-body'>
                                @csrf
                                <div class='form-group'>
                                    <label for='file'>Upload file</label>
                                    <input name='up_file' id='input-2' type='file' class='file' accept="image/*, video/*"
                                        data-max-file-size="{{ config('horizontcms.max_upload_file_size', 2560) }}KB"
                                        multiple='true' data-show-upload='false' data-show-caption='true' required>


                                    @error('up_file')
                                        <div class="text-danger" role="alert">
                                            <strong>{{ $errors->first('up_file') }}</strong>
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="header-image-title" class="form-label">Tagline</label>
                                    <input type="text" class="form-control" name="title" id="header-image-title">
                                </div>
                                <div class="mb-3">
                                    <label for="header-image-title" class="form-label">Link</label>
                                    <input type="text" class="form-control" name="link" id="header-image-title">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleFormControlTextarea1" class="form-label">Summary</label>
                                    <textarea class="form-control" name="description" id="exampleFormControlTextarea1" rows="5"></textarea>
                                </div>

                            </div>
                            <div class='modal-footer'>
                                <button type='submit' name="active" value="0"
                                    class='btn btn-primary'>{{ trans('actions.upload') }}</button>
                                <button type='button' class='btn btn-default'
                                    data-bs-dismiss='modal'>{{ trans('actions.cancel') }}</button>
                            </div>
                        </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

        </div>
    </div>

@endsection
