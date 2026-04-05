@extends('layout', ['title' => trans('SocialMedia')])

@section('content')
    <div class='container main-container'>

        <div class="card mb-3">
            @include('breadcrumb', [
                'links' => [
                    ['name' => trans('settings.settings'), 'url' => route('settings.index')],
                    ['name' => trans('settings.social_media'), 'url' => route('settings.show', ['setting' => 'socialmedia'])]
                ],
                'page_title' => trans('Social media'),
            ])
            <div class="card-body">
                <form action='{{ route('settings.store') }}' role='form' method='POST'>
                    @csrf

                    <table id='settings' class="w-100">

                        <tbody class="text-center fw-bold">

                            @foreach ($all_socialmedia as $each)
                                <tr>
                                    <td>
                                        <i class='fa fa-{{ $each->getName() }}"' aria-hidden='true'></i>
                                        {{ ucfirst($each->getName()) }}
                                    </td>
                                    <td>
                                        <input type='text' class='form-control' name='{{ $each->setting }}'
                                            value='{{ htmlspecialchars($each->value) }}'>
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td></td>
                                <td>
                                    <button type='submit' class='btn btn-primary'>
                                        <i class="fa-solid fa-floppy-disk"></i> 
                                        {{ trans('settings.adminarea_save_settings') }}
                                    </button> 
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection
