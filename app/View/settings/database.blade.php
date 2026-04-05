@extends('layout', ['title' => trans('settings.database')])

@section('content')
    <div class='container'>
        <div class="card mb-3">

            @include('breadcrumb', [
                'links' => [
                    ['name' => trans('settings.settings'), 'url' => route('settings.index')],
                    ['name' => trans('settings.database'), 'url' => route('settings.show', ['setting' => 'database'])]
                ],
                'page_title' => trans('settings.database'),
                'stats' => [['label' => 'driver', 'value' => config('database.default')]],
            ])

            <div class="card-body">
                <table class='table table-bordered table-hover'>
                    <thead>
                        <th class="col-md-8">Table</th>
                        <th class="col-md-4">Action</th>
                    </thead>
                    <tbody>

                        @foreach ($tables as $table)
                            @foreach ($table as $key => $value)
                                <tr>
                                    <td class="col-md-8"><b>{{ $value }}</b></td>
                                    <td class="col-md-4"> </td>
                                </tr>
                            @endforeach
                        @endforeach


                    </tbody>
                </table>
            </div>
        </div>
    @endsection
