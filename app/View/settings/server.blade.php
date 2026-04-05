@extends('layout', [ 'title' => trans('Server') ])

@section('content')
    <div class='container'>
        <div class="card mb-3">

            @include('breadcrumb', [
                'links' => [
                    ['name' => trans('settings.settings'), 'url' => route('settings.index')],
                    ['name' => trans('settings.server'), 'url' => route('settings.show', ['setting' => 'server'])]
                ],
                'page_title' => trans('Server Information'),
            ])

            <div class="card-body">

                <table class='table table-bordered'>
                    <thead>
                        <tr class="d-flex">
                            <th class="col-md-4">Name</th>
                            <th class="col-md-8">Value</th>
                        </tr>

                    </thead>
                    <tbody>

                        @foreach ($server as $key => $value)
                            <tr class="d-flex">
                                <td class="col-md-4"><b>{{ $key }} : </b></td>
                                <td class="col-md-8" style="word-break: break-all;">{{ $value }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>
    @endsection
