@extends('layout', [
    'title' => isset($blogpost)? trans('blogpost.edit_blogpost') : trans('blogpost.new_blogpost') 
])

@section('content')
    <div class='container main-container'>
        <div class="card mb-3">
            @include('breadcrumb', ['links' => [['name'=> trans('dashboard.content')], ['name'=> 'Blog', 'url' => route('blogpost.index')]], 'page_title' => trans(isset($blogpost) ? 'blogpost.edit_blogpost' : 'blogpost.new_blogpost') ] )
            <div class="card-body">

                <form role='form'
                    action="{{ isset($blogpost) ? route('blogpost.update', ['blogpost' => $blogpost]) : route('blogpost.store') }}"
                    method='POST' enctype='multipart/form-data'>

                    @csrf
                    @if (isset($blogpost))
                        @method('PUT')
                    @endif

                    <div class="row">

                        <div class="col-xs-12 col-md-8">

                            <div class='form-group col mb-4'>
                                <label for='title'>{{ trans('blogpost.title') }}</label>
                                <input type='text' class='form-control form-control-lg @error('title') is-invalid @enderror' id='title' name='title'
                                    value="{{ old('title', isset($blogpost) ? $blogpost->title : '') }}" required>

                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="row">
                            <div class='form-group col-12 mb-4'>

                                <category-selector :label="'{{ trans('blogpost.select_category') }}'" :blogpost_categories='@json(isset($blogpost)? $blogpost->categories : [])'></category-selector>

                            </div>

                            

                            </div>
                            <div class="row">
                            @can('update', 'user')
                                <div class='form-group col-6 mb-4'>
                                    <label for='sel1'>{{ trans('blogpost.author') }}</label>
                                    <select class='form-select' name='author_id' id='sel1'>

                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ (isset($blogpost->author) && $user->is($blogpost->author)) || ($user->is(auth()->user())) ? 'selected' : '' }}>
                                                {{ $user->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            @endcan
                            <div class='form-group pull-left col-xs-12 col-md-6'>
                                <label for='title'>{{ trans('settings.adminarea_language') }}</label>
                                <select class='form-select' name='language'>

                                    @foreach (config('horizontcms.languages') as $key => $value)
                                        <option value='{{ $key }}'
                                            @if ((isset($blogpost) && $key == $blogpost->language) || (!isset($blogpost) && $key == \Settings::get('language'))) selected @endif>
                                            {{ $value }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                            </div>

                            <div class='form-group col mb-4'>
                                <label for='title'>{{ trans('blogpost.summary') }}</label>
                                <textarea type='text' maxlength="255" rows="3" class='form-control @error('summary') is-invalid @enderror' id='summary' name='summary'>{{ old('summary', isset($blogpost) ? $blogpost->summary : '') }}</textarea>
                                </br>

                                @error('summary')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('summary') }}</strong>
                                    </span>
                                @enderror
                            </div>

                        </div>
                        <div class="col-md-4 col-sm-12">

                            @if (isset($blogpost) && $blogpost->hasImage())
                                <button type='button' class='btn btn-link mb-5 w-100' data-bs-toggle='modal'
                                    data-bs-target='#modal-xl-{{ $blogpost->id }}'>
                                @if($blogpost->getFeaturedMediaType()==='video')
                                    <video controls class="w-100" style="max-height:500px;">
                                        <source src="{{ $blogpost->getImage()}}">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img src='{{ $blogpost->getThumb() }}' class='img img-thumbnail w-100'>
                                @endif
                                </button>
                            @endif

                            <div class='form-group'>
                                <label for='file'>{{ trans('actions.upload_image') }}</label>
                                <input name='up_file' accept='image/*' id='input-2' type='file' class='file'
                                    multiple='true'
                                    data-max-file-size="{{ config('horizontcms.max_upload_file_size', 2560) }}KB"
                                    data-drop-zone-enabled="{{ isset($blogpost) && $blogpost->hasImage() ? 'false' : 'true' }}"
                                    data-remove-class="btn btn-default" data-show-upload='false' data-show-caption='true'>
                            </div>


                            @error('up_file')
                                <div class="text-danger" role="alert">
                                    <strong>{{ $errors->first('up_file') }}</strong>
                                </div>
                            @enderror

                        </div>

                        <div class="col-12">
                            <div class='form-group pull-left col-12'>
                                <label for='text'>{{ trans('blogpost.post') }}</label>
                                <text-editor id="texteditor" :name="'text'"
                                    :data="'{{ remove_linebreaks(old('blogpost', isset($blogpost) ? str_replace("'", "&#39;", $blogpost->text) : '')) }}'"
                                    :language="'{{ config('app.locale') }}'"
                                    :filebrowserBrowseUrl="'{{ route('filemanager.index', ['path' => 'images/blogposts', 'mode' => 'embed']) }}'"
                                    :filebrowserUploadUrl="'{{ route('file-manager.store', ['dir_path' => 'storage/images/blogposts']) }}'">
                                </text-editor>
                            </div>

                            <div class='form-group col-12'>
                                
                                @if (!isset($blogpost))
                                    <button name="active" value="1" id='submit-btn' type='submit'
                                        class='btn btn-primary btn-lg'
                                        onclick='window.onbeforeunload = null;'>{{ trans('actions.publish') }}</button>
                                    <button name="active" value="0" id='submit-btn' type='submit'
                                        class='btn btn-info'
                                        onclick='window.onbeforeunload = null;'>{{ trans('actions.save_draft') }}</button>
                                @else
                                    <button id='submit-btn' name='active'
                                        value="{{ isset($blogpost) && $blogpost->isDraft() ? 0 : 1 }}" type='submit'
                                        class='btn btn-success btn-lg'
                                        onclick='window.onbeforeunload = null;'>{{ trans('actions.update') }}</button>
                                    @if ($blogpost->isDraft())
                                        <button name="active" value="1" id='submit-btn' name='submit_clicked'
                                            type='submit' class='btn btn-primary btn-lg'
                                            onclick='window.onbeforeunload = null;'>{{ trans('actions.publish') }}</button>
                                    @endif

                                    <a name="active" target="_blank" href="{{ url(config('theme::theme.content.blogpost.preview.url', 'blogposts').'/'.$blogpost->getSlug()) }}" type='button'
                                        class='btn btn-secondary'
                                        onclick='window.onbeforeunload = null;'>{{ trans($blogpost->isDraft()? 'Preview': 'View') }}</a>
                                @endif
                                <a href="{{ route('blogpost.index') }}" type='button'
                                    class='btn btn-default float-end'>{{ trans('actions.cancel') }}</a>
                            </div>
                        </div>

                    </div>

                </form>
            </div>

        </div>
    </div>

    @if (isset($blogpost) && $blogpost->hasImage())
        @include('image_details', ['modal_id' => $blogpost->id, 'image' => $blogpost->getImageFilePath()])
    @endif

@endsection

@section('head')
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type='text/javascript' defer>

            $(document).ready(function() {

                $("#category_select").select2({
                    theme: "bootstrap-5",
                });

            });
    </script>
    
@endsection