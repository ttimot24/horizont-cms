@extends('layout', ['title' => trans('settings.settings')])

@section('content')
    <div class='container main-container'>

        <div class="card">

            @include('breadcrumb', [
                'links' => [
                    ['name' => trans('settings.settings'), 'url' => route('settings.index')],
                    ['name' => trans('settings.adminarea_settings'), 'url' => route('settings.show', ['setting' => 'adminarea'])]
                ],
                'page_title' => trans('settings.adminarea_settings'),
            ])

            <div class="card-body">
                <form action='{{ route('settings.store') }}' role='form' method='POST'>
                    @csrf

                    <input type='hidden' name='is_actioned' value='1'>



                    <div class="card mb-3 border-secondary">
                        <div class="card-header text-bg-light fs-3">Options</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">{{ trans('settings.adminarea_theme') }}</h6>
                                    <p class="text-muted">You can modify the admin area theme and design.</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <select name='admin_theme' class='form-select' disabled>
                                        <option value=''>default</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">{{ trans('settings.adminarea_dashboard_logo') }}</h6>
                                    <p class="text-muted">You can change the logo that is shown on the dashboard.</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3 text-center">
                                    <div class="col-12 mb-3">
                                        <input type="hidden" name="admin_logo"
                                            value="<?= $settings['admin_logo'] != '' && file_exists('storage/images/logos/' . $settings['admin_logo']) ? $settings['admin_logo'] : \Config::get('horizontcms.admin_logo') ?>">
                                        <img id="admin_logo" class='well well-sm'
                                            src="<?= $settings['admin_logo'] != '' && file_exists('storage/images/logos/' . $settings['admin_logo']) ? 'storage/images/logos/' . $settings['admin_logo'] : \Config::get('horizontcms.admin_logo') ?>"
                                            height='100'>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type='button' class='btn btn-success btn-sm' data-bs-toggle='modal'
                                            data-bs-target='.admin_logo_select-modal-lg'>{{ trans('actions.select') }}</button>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">{{ trans('settings.adminarea_language') }}</h6>
                                    <p class="text-muted">You can set the admin area language globally.</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <select name='language' class='form-select'>

                                        @foreach (config('horizontcms.languages') as $key => $language)
                                            <option value='{{ $key }}' {{ $key == $settings['language']? "selected" : "" }}>{{ ucfirst($language) }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">{{ trans('settings.adminarea_date_format') }}</h6>
                                    <p class="text-muted">You can change the dateformat on admin area.</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <select name='date_format' class='form-select'>

                                        @foreach ($dateFormats as $format)
                                            @if ($format == $settings['date_format'])
                                                <option value='{{ $format }}' selected>{{ date($format) }}</option>
                                            @else
                                                <option value='{{ $format }}'>{{ date($format) }}</option>
                                            @endif
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">{{ trans('settings.adminarea_auto_update_check') }}</h6>
                                    <p class="text-muted">Turns on and off automatic upgrade check.</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <input type="hidden" name="auto_upgrade_check" value="0"> <!-- Checkbox hack -->
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input type='checkbox' class='form-check-input' name='auto_upgrade_check'
                                            value='1' @if ($settings['auto_upgrade_check'] == 1) checked @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Store URL</h6>
                                    <p class="text-muted">The plugin and theme repository URL.</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <input type='text' class='form-control' value="{{ config('horizontcms.sattelite_url') }}" disabled>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">{{ trans('settings.adminarea_broadcast_message') }}</h6>
                                    <p class="text-muted">Write anything here, this will be displayed across the admin area
                                        constantly.</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <textarea type='text' class='form-control' name='admin_broadcast' rows='2'>{{ $settings['admin_broadcast'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type='submit' class='btn btn-primary btn-lg'><i class="fa-solid fa-floppy-disk"></i> {{ trans('settings.adminarea_save_settings') }}</button>
                    </div>
                </form>



                <div class='modal admin_logo_select-modal-lg' tabindex='-1' role='dialog'
                    aria-labelledby='myLargeModalLabel' aria-hidden='true'>
                    <div class='modal-dialog modal-xl'>
                        <div class='modal-content'>

                            <div class='modal-header'>
                                <h3 class='modal-title'>{{ trans('settings.adminarea_select_logo') }}</h3>

                                <button id="close-modal" type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>
                            <div class='modal-body' style="max-height:75vh; overflow-y: scroll;">

                                @include('media.filemanager', [
                                    'mode' => 'embed',
                                    'current_dir' => 'storage/images/logos',
                                ])

                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>

    </div>
@endsection

@section('head')
    <script defer>
        window.onload = function() {

            $("#workspace").on('click', ".file", function(event) {
                var src = $(event.target).attr('src');
                var bname = src.substring(src.lastIndexOf('/') + 1);
                $('[name="admin_logo"]').val(bname);
                $('#admin_logo').attr('src', 'storage/images/logos/' + bname);
                $('#close-modal').click();
            });

        }
    </script>
@endsection
