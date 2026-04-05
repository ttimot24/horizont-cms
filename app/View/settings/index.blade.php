@extends('layout', ['title' => trans('settings.settings')])

@section('content')
    <div class="container main-container">

        <div class="card">
            @include('breadcrumb', [
                'links' => [['name' => trans('settings.settings'), 'url' => route('settings.index')]],
                'page_title' => trans('settings.settings'),
            ])
            <div class="card-body">

                <div class="container col-md-12">

                    <div class="row">
                        @foreach ($panels as $each)
                            <div class='col-md-3 text-center mb-5 bg-dark py-4'>
                                <a href="{{ $each->getLink() }}" class="text-decoration-none">
                                    <i class="{{ $each->getIcon() }} text-white mb-2" style='font-size:60px;'></i>
                                    <h4 class="text-white">{{ $each->getName() }}</h4>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
    <br><br>
@endsection
