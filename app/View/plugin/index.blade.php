@extends('layout', ['title' => trans('Applications')])

@section('content')
    <div class='container main-container'>

        <div class="card mb-3">

            @include('breadcrumb', [
                'links' => [['name' => trans('dashboard.content')], ['name' => trans('Plugins'), 'url' => route('plugin.index')]],
                'page_title' => trans('Plugin manager'),
                'stats' => [['label' => trans('user.all'), 'value' => $all_plugin->count()]],
                'buttons_right' => [
                    [
                        'icon' => 'fa-cloud-download',
                        'label' => 'Download apps',
                        'route' => route('pluginregistry.index'),
                        'class' => 'btn-info',
                    ],
                    [
                        'icon' => 'fa-upload',
                        'label' => 'Upload plugin',
                        'class' => 'btn-primary',
                        'data' =>
                            'data-bs-toggle=modal data-bs-target=.upload_plugin ' .
                            ($zip_enabled ? '' : 'disabled'),
                    ],
                ],
            ])


            <div class="card-body">

                @foreach ($all_plugin as $current_plugin)
                    <div class='card mb-3'>
                        <div class='card-body p-3 bg-dark'>
                            <div class='row p-0'>
                                <div class='col-md-1 col-sm-12 col-xs-12 p-0 pl-3 text-center'>
                                    <img src="{{ $current_plugin->getImage() }}" class='img img-thumbnail mt-1'
                                        style='width: 5rem; height: 5rem;' />
                                </div>

                                <div class='col-md-9 m-0'>
                                    <h4 class='p-0'>

                                        @if ($current_plugin->isActive())
                                            <a class='card-title text-primary' id='{{ $current_plugin->root_dir }}'
                                                href='{{ route('plugin.' . str_slug($current_plugin->root_dir) . '.start.index') }}'>{{ $current_plugin->getName() }}</a>
                                        @else
                                            <a class='text-white'
                                                id='{{ $current_plugin->root_dir }}'>{{ $current_plugin->getName() }}</a>
                                        @endif


                                        <small class='text-muted'>version: {{ $current_plugin->getInfo('version') }}
                                            | author: <a href='{{ $current_plugin->getInfo('author_url') }}'>
                                                {{ $current_plugin->getInfo('author') }}</a>
                                        </small>
                                    </h4>

                                    <p class='text-white'>
                                        {{ $current_plugin->getInfo('description') }}
                                    </p>

                                    @if (!$current_plugin->isCompatibleWithCore())
                                        <p class='text-danger m-0 p-0'>
                                            Required core version: v{{ $current_plugin->getRequiredCoreVersion() }}
                                        </p>
                                    @endif

                                </div>

                                <div class='col-md-2 col-sm-4 col-xs-4 text-end'>

                                    <div class="row align-items-center">
                                        @if ($current_plugin->isUpdatable())
                                            @can('update', 'plugin')
                                                <div class="col-6 mb-3">
                                                    <form action="{{ route('plugin.update', ['plugin' => 1]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="plugin_name"
                                                            value="{{ $current_plugin->root_dir }}">
                                                        <button type="submit" id='install' name="upgrade" value="1"
                                                            class='btn btn-primary btn-block w-100'>Update</button>
                                                    </form>
                                                </div>
                                            @endcan
                                        @endif
                                    @if (!$current_plugin->isInstalled() && $current_plugin->isCompatibleWithCore())
                                            @can('create', 'plugin')
                                                <div class="col-6">
                                                    <form action="{{ route('plugin.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="plugin"
                                                            value="{{ $current_plugin->root_dir }}">
                                                        <button type="submit" id='install'
                                                            class='btn btn-primary btn-block w-100'>Install</button>
                                                    </form>
                                                </div>
                                            @endcan
                                        @elseif($current_plugin->isInstalled())
                                            @can('update', 'plugin')
                                                <div class="col-6">

                                                    <form action="{{ route('plugin.update', ['plugin' => 1]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="plugin_name"
                                                            value="{{ $current_plugin->root_dir }}">
                                                        @if (!$current_plugin->isActive())
                                                        <button type="submit" id='update'  name="active" value="1"
                                                            class='btn btn-success btn-block w-100'>Activate</button>
                                                        @else
                                                        <button type="submit" id='update' name="active" value="0"
                                                            class='btn btn-info btn-block w-100'>Deactivate</button>
                                                        @endif
                                                    </form>
                                                    
                                                </div>
                                            @endcan
                                    @endif


                                    @can('delete', 'plugin')
                                        <div class="col-6">
                                            <button class='btn btn-danger btn-block w-100' data-bs-toggle='modal'
                                                data-bs-target='#delete_{{ $current_plugin->root_dir }}'>{{ trans('actions.delete') }}</button>
                                        </div>
                                    @endcan
                                </div>
                            </div>

                        </div>

                    </div>

            </div>

            @can('delete', 'plugin')
                @include('confirm_delete', [
                    'route' => route('plugin.destroy', ['plugin' => $current_plugin->root_dir]),
                    'id' => 'delete_' . $current_plugin->root_dir,
                    'header' => trans('actions.are_you_sure'),
                    'name' => $current_plugin->getName(),
                    'content_type' => 'plugin',
                    'delete_text' => trans('actions.delete'),
                    'cancel' => trans('actions.cancel'),
                ])
            @endcan
            @endforeach


            <div class='modal upload_plugin' id='create_file' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'
                aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header modal-header-primary bg-primary'>
                            <h4 class='modal-title text-white'>New file</h4>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>

                            <form action="{{ config('horizontcms.backend_prefix') }}/plugin/upload" method='POST'
                                enctype='multipart/form-data'>
                                @csrf
                                <div class='form-group'>
                                    <label for='file'>Upload file:</label>
                                    <input name='up_file[]' id='input-2' type='file' class='file' accept='.zip'
                                        multiple='true' data-show-upload='false' data-show-caption='true'>
                                </div>


                        </div>
                        <div class='modal-footer'>
                            <button type='submit' class='btn btn-primary'>Upload</button></form>
                            <button type='button' class='btn btn-default'
                                data-bs-dismiss='modal'>{{ trans('actions.cancel') }}</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->



        </div>

    </div>
@endsection
