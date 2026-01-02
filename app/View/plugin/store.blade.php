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
                                        <p><a href="admin/plugin/download-plugin/{{ $o_plugin->dir }}"
                                                class="btn btn-primary btn-block btn-sm" role="button">Upgrade</a></p>
                                    @elseif(isset($o_plugin->local) && !$o_plugin->local->isInstalled())
                                        <p><a href="admin/plugin/install/{{ $o_plugin->dir }}"
                                                class="btn btn-success btn-block btn-sm" role="button">Install</a></p>
                                    @elseif(isset($o_plugin->local) && $o_plugin->local->isInstalled())
                                        <p style='height: 30px;'><b>Installed</b></p>
                                    @else
                                        <p><a href="admin/plugin/download-plugin/{{ $o_plugin->dir }}"
                                                class="btn btn-info btn-block btn-sm" role="button">Download</a></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

            </div>

        </div>
    @endsection
