@extends('layout', ['title' => trans('App center')])

@section('content')
    <div class='container main-container'>

        <div class="card mb-3">

            @include('breadcrumb', [
                'links' => [
                    ['name' => trans('dashboard.content')],
                    ['name' => trans('Plugin manager'), 'url' => route('plugin.index')],
                ],
                'page_title' => trans('Online Store'),
                'stats' => [['label' => trans('user.all'), 'value' => $online_plugins->count()]],
            ])

            <div class="card-body">

                <div class="row">

                    @if (!$repo_status)
                        <div class="alert alert-warning" role="alert">
                            <div><b>Warning</b> Plugin store unreachable!</div>
                        </div>
                    @endif

                    @foreach ($online_plugins as $o_plugin)

                        <div class="col-sm-6 col-md-3 mb-3">
                            <div class="card  bg-dark p-2 text-white">
                                <img src="{{ $o_plugin->icon }}" class="img w-100" style='height:10rem;object-fit:cover;'
                                    alt="...">
                                <div class="caption">
                                    <h3 class="mt-3">{{ $o_plugin->info->name }}</h3>
                                    <p>version: {{ $o_plugin->info->version }} author: {{ $o_plugin->info->author }}</p>

                                    @if ($o_plugin->local && $o_plugin->local->getInfo('version') < $o_plugin->info->version)
                                            @can('update', 'pluginregistry')
                                                <form action="{{ route('pluginregistry.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="plugin_name"
                                                        value="{{ $o_plugin->dir }}">
                                                    <button type="submit" id='install'
                                                        class='btn btn-primary btn-block w-100'>Upgrade</button>
                                                </form>
                                            @endcan
                                    @elseif(isset($o_plugin->local) && !$o_plugin->local->isInstalled())
                                            @can('create', 'plugin')
                                                    <form action="{{ route('plugin.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="plugin"
                                                            value="{{ $o_plugin->dir }}">
                                                        <button type="submit" id='install'
                                                            class='btn btn-success btn-block w-100'>Install</button>
                                                    </form>
                                            @endcan
                                    @elseif(isset($o_plugin->local) && $o_plugin->local->isInstalled())
                                        <p style='height: 30px;'><b>Installed</b></p>
                                    @else
                                            @can('create', 'pluginregistry')
                                                <form action="{{ route('pluginregistry.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="plugin_name"
                                                        value="{{ $o_plugin->dir }}">
                                                    <button type="submit" id='install'
                                                        class='btn btn-info btn-block w-100'>Download</button>
                                                </form>
                                            @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

            </div>

        </div>
    @endsection
