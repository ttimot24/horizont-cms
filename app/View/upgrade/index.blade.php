@extends('layout', [
    'title' => trans('settings.settings')
])

@section('content')
    <div class='container main-container'>

        <div class="card mb-3">

            @include('breadcrumb', [
                'links' => [
                    ['name' => trans('settings.settings'), 'url' => route('settings.index')],
                    ['name' => trans('System Update Center'), 'url' => route('upgrade.index')]
                ],
                'page_title' => trans('System Update Center'),
                'stats' => [['label' => 'Current version', 'value' => $current_version]],
            ])

            <div class="card-body">
                <section class='row'>

                    <div class='col-md-4'>

                        <ul class="list-group">

                            @foreach ($available_list as $available)
                                <li
                                    class="list-group-item {{ $available['tag_name'] === $current_version ? 'active' : '' }}">
                                    <h5 class="list-group-item-heading mt-3">Available update: {{ $available['tag_name'] }}
                                    </h5>

                                    <div class="row list-group-item-text">
                                        <div class="col-8">Released at: {{ $available['published_at'] }}</div>

                                        <div class="col-4 text-end">
                                            <form class="m-0 p-0" method="POST"
                                                action="{{ route('upgrade.update', $available['tag_name']) }}">
                                                @method('PUT')
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">Upgrade</button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            @endforeach


                            @foreach ($upgrade_list as $available)
                                <li
                                    class="list-group-item {{ $available['tag_name'] === $current_version ? 'active' : '' }}">
                                    <h5 class="list-group-item-heading mt-3">Previous version: {{ $available['tag_name'] }}
                                    </h5>
                                    <p class="list-group-item-text">Released at: {{ $available['published_at'] }}</p>
                                </li>
                            @endforeach

                        </ul>
                    </div>

                    <div class='col-md-8'>
                        <div class="card">
                            <div class="card-body" style='min-height:400px;'>
                                <p>
                                    {!! $selected_version? \Str::of($selected_version['body'])->markdown() : '' !!}
                                </p>
                            </div>
                        </div>
                    </div>

                </section>

            </div>

        </div>
    @endsection
