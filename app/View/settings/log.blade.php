@extends('layout', ['title' => trans("Log files")])

@section('content')
    <div class='container card'>

        @include('breadcrumb', [
            'links' => [['name' => trans('settings.settings'), 'url' => route('settings.index')], ['name' => trans('Log'), 'url' => route('log.index')], ['name' => $current_file, 'url' => route('log.show', ['log' => $current_file])]],
            'page_title' => 'System log',
            'stats' => [
                ['label'=> 'Files', 'value' => $all_files->count()]
            ]
        ])

        <div class="card-body">
        <section class="row">


            <div class='col-3'>
                <div class="list-group">
                    @foreach ($all_files as $file)
                        <a href="{{ route('log.show', ['log' => basename($file)]) }}"
                            class="list-group-item @if (basename($file) == basename($current_file)) bg-primary border-0 text-white @endif">{{ basename($file) }}</a>

                        @if ($entries->total() == $max_files)
                        @break
                    @endif
                @endforeach
            </div>
        </div>
        <div class='col-9'>

            <?php
            
            $colors = [
                'emergency' => 'emergency',
                'alert' => 'alert',
                'critical' => 'danger',
                'error' => 'danger',
                'warning' => 'warning',
                'notice' => 'info',
                'info' => 'info',
                'debug' => 'info',
            ];
            
            ?>

            <div class="card bg-dark text-white mb-4 p-4">
                <div class="row">
                    <div class="col-6">
                        <h4>Entries: {{ $entries->total() }}</h4>
                    </div>
                    <div class='col-6 text-end'>
                        @if (isset($current_file))
                            <a href="{{ 'storage/framework/logs/' . $current_file }}" class="btn btn-primary"><i
                                    class="fa fa-download" aria-hidden="true"></i> Download file</a>
                        @endif
                    </div>
                </div>
                <div class="row p-2">
                    {{ 'storage/framework/logs/' . $current_file }}
                </div>
            </div>


            <div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">

                @foreach ($entries as $entry)
                    <div class="accordion-item">
                        <h2 class="accordion-header mt-0" id="heading{{ $entry->id }}">
                            <button class="accordion-button bg-{{ $colors[$entry->level] }} text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $entry->id }}"
                                aria-expanded="true" aria-controls="collapse{{ $entry->id }}">
                                <div class='col-8'>
                                    #{{ $entry->id}} <i class="fa fa-exclamation-triangle pl-2"
                                        aria-hidden="true"></i> {{ ucfirst($entry->level) }} - {{ $entry->id }}
                                </div>
                                <div class='col-4 text-end'>
                                    {{ $entry->date->format(\Settings::get('date_format', \Config::get('horizontcms.default_date_format'), true)) }}
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $entry->id }}" class="accordion-collapse collapse show"
                            aria-labelledby="heading{{ $entry->id }}" data-bs-parent="#accordion">
                            <div class="accordion-body bg-dark text-white">
                                {!! $entry->context !!}
                            </div>
                        </div>
                    </div>

                @endforeach

            </div>

            <div class="d-flex justify-content-center mt-5">
                {{ $entries->withPath(route('log.show', ['log' => $current_file]))->links() }}
            </div>

        </div>

    </section>
    </div>
</div>
@endsection
