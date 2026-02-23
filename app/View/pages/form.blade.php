@extends('layout', ['title' => isset($page)? trans('page.edit_page') : trans('page.new_page')])

@section('content')
    <div class='container main-container'>

        <div class="card mb-3">
                    @include('breadcrumb', [
                        'links' => [['name' => trans('dashboard.content')], ['name' => trans('page.pages'), 'url' => route('page.index')]],
                        'page_title' => trans(isset($page) ? 'page.edit_page' : 'page.add_new_page_title'),
                    ])
            <div class="card-body">

                <form role='form'
                    action="{{ isset($page) ? route('page.update', ['page' => $page]) : route('page.store') }}" method='POST'
                    enctype='multipart/form-data'>

                    @csrf
                    @if (isset($page))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-xs-12 col-8">
                            <div class='form-group pull-left col-xs-12 col-md-12'>
                                <label for='title'>{{ trans('page.menu_name') }}</label>
                                <input type='text' class='form-control form-control-lg' id='menu-title' name='name'
                                    onkeyup="generateSlug(this.value)" value="{{ old('name', isset($page) ? $page->name : '') }}"
                                    required>
                                <div class="form-text">
                                    <b>{{ trans('page.semantic_url') }}:</b>&nbsp&nbsp&nbsp{{ rtrim(config('app.url'), '/') }}<a
                                        class='text-muted'
                                        id='generatedSlug'>{{ isset($page) ? UrlManager::seo_url($page->name) : '' }}</a>
                                </div>
                            </div>

                            <div class="row p-3">
                            <div class='form-group pull-left col-xs-12 col-md-8'>
                                <label for='title'>{{ trans('page.page_template') }}</label>
                                <select class='form-select' name='url'>
                                    <option value=''>{{ trans('page.default_template') }}</option>

                                    @foreach ($page_templates as $template)
                                        <option value='{{ $template }}'
                                            @if (isset($page) && $template == $page->url) selected @endif>
                                            {{ ucfirst(basename(basename($template, '.php'), '.blade')) }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class='form-group pull-left col-xs-12 col-md-4'>
                                <label for='title'>{{ trans('settings.adminarea_language') }}</label>
                                <select class='form-select' name='language'>

                                    @foreach (config('horizontcms.languages') as $key => $value)
                                        <option value='{{ $key }}'
                                            @if ((isset($page) && $key == $page->language) || (!isset($page) && $key == \Settings::get('language'))) selected @endif>
                                            {{ $value }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                            </div>

                            <div class="row p-3">
                                <parent-page-selector :label="'{{ trans('page.page_level') }}'" :languages='@json(config('horizontcms.languages'))' :page='@json(isset($page)? $page : null)' :all_pages='@json($all_page)' />
                            </div>

                            <div class='form-group col-xs-12 col-md-12' style='margin-top:20px;margin-bottom:20px;'>
                                <label class="m-2 mr-4">{{ trans('page.visibility') }}</label>
                                <div class='form-check form-check-inline'>
                                    <input class="form-check-input" type='radio' id='inlineRadio1' value='1'
                                        name='visibility' @if (!isset($page) || (isset($page) && $page->visibility == 1)) checked @endif>
                                    <label class="form-check-label" for='inlineRadio1'> {{ trans('page.visible') }}
                                    </label>
                                </div>
                                <div class='form-check form-check-inline'>
                                    <input class="form-check-input" type='radio' id='inlineRadio2' value='0'
                                        name='visibility' @if (isset($page) && $page->visibility == 0) checked @endif>
                                    <label class="form-check-label" for='inlineRadio2'> {{ trans('page.invisible') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">

                            @if (isset($page) && $page->hasImage())
                                <button type='button' class='btn btn-link mb-5 w-100' data-bs-toggle='modal'
                                    data-bs-target='#modal-xl-{{ $page->id }}'>
                                    @if($page->getFeaturedMediaType()==='video')
                                        <video controls class="w-100" style="max-height:500px;">
                                            <source src="{{ $page->getImage()}}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <img src='{{ $page->getThumb() }}' class='img img-thumbnail w-100'>
                                    @endif
                                </button>
                            @endif

                            <div class='form-group'>
                                <label for='file'>{{ trans('actions.upload_image') }}:</label>
                                <input name='up_file' accept='image/*' id='input-2' type='file' class='file'
                                    multiple='true'
                                    data-max-file-size="{{ config('horizontcms.max_upload_file_size', 2560) }}KB"
                                    data-drop-zone-enabled="{{ isset($page) && $page->hasImage() ? 'false' : 'true' }}"
                                    data-show-upload='false' data-show-caption='true'>

                                
                                @error('up_file')
                                    <div class="text-danger" role="alert">
                                        <strong>{{ $errors->first('up_file') }}</strong>
                                    </div>
                                @enderror
                            </div>

                        </div>


                        <div class='form-group pull-left col-xs-12 col-md-12'>
                            <label for='text'>{{ trans('page.page_content') }}</label>

                            <text-editor id="texteditor" :name="'page'"
                                :data="'{{ remove_linebreaks(old('page', isset($page) ? str_replace("'", "&#39;", $page->page) : '')) }}'"
                                :language="'{{ config('app.locale') }}'"
                                :filebrowserBrowseUrl="'{{ route('filemanager.index', ['path' => 'images/pages', 'mode' => 'embed']) }}'"
                                :filebrowserUploadUrl="'{{ route('file-manager.store', ['dir_path' => 'storage/images/pages']) }}'">
                            </text-editor>

                        </div>

                        <div class='form-group pull-left col-xs-12 col-md-8'>
                            <button id='submit-btn' type='submit'
                                class='btn btn-primary btn-lg'>{{ trans(isset($page) ? 'actions.update' : 'actions.publish') }}</button>
                        </div>
                    </div>




                </form>

            </div>
        </div>

    </div>

    @if (isset($page) && $page->hasImage())
        @include('image_details', ['modal_id' => $page->id, 'image' => $page->getImage()])
    @endif

@endsection