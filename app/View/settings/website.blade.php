@extends('layout', [ 'title' => trans('settings.settings')])

@section('content')
    <div class='container main-container'>

        <div class="card mb-3">

            @include('breadcrumb', [
                'links' => [
                    ['name' => trans('settings.settings'), 'url' => route('settings.index')],
                    ['name' => trans('settings.website'), 'url' => route('settings.show', ['setting' => 'website'])]
                ],
                'page_title' => trans('Website settings'),
            ])

            <div class="card-body">
                <form action='{{ route('settings.store') }}' role='form' method='POST'>
                    @csrf


                    <div class="card mb-3 border-secondary">
                        <div class="card-header text-bg-light fs-3">Information</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Title</h6>
                                    <p class="text-muted">This is going to be the default title in the browser tab.</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <input type='text' class='form-control' name='title'
                                        value="{{ $settings['title'] }}">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Site name</h6>
                                    <p class="text-muted">The name of your website</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <input type='text' class='form-control' name='site_name'
                                        value="{{ $settings['site_name'] }}">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Slogan</h6>
                                    <p class="text-muted">The slogan of your service</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <input type='text' class='form-control' name='slogan'
                                        value="{{ $settings['slogan'] }}">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Broadcast message</h6>
                                    <p class="text-muted">Any message that should appear constantly on yo website</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <input type='text' class='form-control' name='scroll_text'
                                        value="{{ $settings['scroll_text'] }}">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Favicon</h6>
                                    <p class="text-muted">Select the icon of your website</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <?php
                                    $favicon = null;
                                    if ($settings['favicon'] != '' && file_exists('storage/images/favicons/' . $settings['favicon'])) {
                                        $favicon = $settings['favicon'];
                                    }
                                    ?>
                                    <div class="row">
                                        <div class="col mb-2 text-center">
                                            <input type="hidden" name="favicon" value="<?= $favicon ?>">
                                            <img id="favicon" src="storage/images/favicons/<?= $favicon ?>"
                                                style="max-height:2.5rem;" alt="Select an image">
                                        </div>
                                        <div class="col">
                                            <button type='button' id="button-favicon" class='btn btn-success btn-sm'
                                                data-bs-toggle='modal'
                                                data-bs-target='#filemanager-modal'>{{ trans('actions.select') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3 border-secondary">
                        <div class="card-header text-bg-light fs-3">Contact</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Email</h6>
                                    <p class="text-muted">The default email of the website. It should be an admin's email
                                        address</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <input type='email' class='form-control' name='default_email'
                                        value="{{ $settings['default_email'] }}">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Phone</h6>
                                    <p class="text-muted">The admin, office or service desk phone number.</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <input type='text' class='form-control' name='default_phone'
                                        value="{{ $settings['default_phone'] }}">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Address</h6>
                                    <p class="text-muted">The address of the location of the office or headquater</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <input type='text' class='form-control' name='address'
                                        value="{{ $settings['address'] }}">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Other</h6>
                                    <p class="text-muted">You can write any other information here, that can help customers
                                        to reach you.</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <textarea rows='7' class='form-control' name='contact' cols='30'>{{ $settings['contact'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3 border-secondary">
                        <div class="card-header text-bg-light fs-3">Technical</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Debug mode</h6>
                                    <p class="text-muted">Here you can switch if error messages are shown or not when an
                                        exception occurs. If turned off the website theme's 500 page will be shown. If
                                        turned on the exception will be shown. It should be turned off for production.</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <input type="hidden" name="website_debug" value="0"> <!-- Checkbox hack -->
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input type='checkbox' class='form-check-input' name='website_debug'
                                            value='1' @if ($settings['website_debug'] == 1) checked @endif>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Website maintenance</h6>
                                    <p class="text-muted">Here you can switch if the website is under maintenance. When it
                                        is switched on only logged in administrators can see the website. The admin area
                                        still be accessed via direct url</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <input type="hidden" name="website_down" value="0"> <!-- Checkbox hack -->
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input type='checkbox' class='form-check-input' name='website_down'
                                            value='1' @if ($settings['website_down'] == 1) checked @endif>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Secure site with SSL (https)</h6>
                                    <p class="text-muted">If its swirched on. The relatice urls of the website will be
                                        forced use https protocol</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <input type="hidden" name="use_https" value="0"> <!-- Checkbox hack -->
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input type='checkbox' class='form-check-input' name='use_https' value='1'
                                            @if ($settings['use_https'] == 1) checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3 border-secondary">
                        <div class="card-header text-bg-light fs-3">Content</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Logo</h6>
                                    <p class="text-muted">Here you can set the main logo of the website</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3 text-center">
                                    <?php
                                    $logo = null;
                                    if ($settings['logo'] != '' && file_exists('storage/images/logos/' . $settings['logo'])) {
                                        $logo = $settings['logo'];
                                    }
                                    ?>
                                    <div class="col-12 mb-3">
                                        <input type="hidden" name="logo" value="<?= $logo ?>">
                                        <img id="logo" src="storage/images/logos/<?= $logo ?>"
                                            style="max-height:7.5rem;" alt="Select an image">
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type='button' id="button-logo" class='btn btn-success btn-sm'
                                            data-bs-toggle='modal'
                                            data-bs-target='#filemanager-modal'>{{ trans('actions.select') }}</button>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Website type</h6>
                                    <p class="text-muted">Here you can change the concept of the website type</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <select name='website_type' class='form-select'>
                                        <option value='website'>Website</option>
                                        <option value='blog'>Blog</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Blogposts on page</h6>
                                    <p class="text-muted">Here you can change how many posts are shown on one page</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <input type='number' min='1' max='100' class='form-control'
                                        name='blogposts_on_page' value="{{ $settings['blogposts_on_page'] }}">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h6 class="text-dark fw-bold">Default role</h6>
                                    <p class="text-muted">Here you can set the default role that will be assigned to a
                                        newly registered user.</p>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <select name='default_user_role' class='form-select'>
                                        @foreach ($user_roles as $role)
                                            <option value='{{ $role->id }}'
                                                @if (isset($settings['default_user_role']) && $role->id == $settings['default_user_role']) selected @endif
                                                @if ($role->isAdminRole()) style='color:red;' @endif>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                    <button type='submit' class='btn btn-primary btn-lg'><i class="fa-solid fa-floppy-disk"></i> {{ trans('settings.adminarea_save_settings') }}</button>
                    </div>
                </form>

                <div id="filemanager-modal" class='modal' tabindex='-1' role='dialog'
                    aria-labelledby='myLargeModalLabelx' aria-hidden='true'>
                    <div class='modal-dialog modal-xl'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h3 class='modal-title text-center'>Select Logo</h3>
                                <button id="close-modal" type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>
                            <div class='modal-body p-3' style="max-height:75vh; overflow-y: scroll;">
                                @include('media.filemanager', [
                                    'mode' => 'embed',
                                    'current_dir' => 'storage/images',
                                ])
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

                    var context = "";

                    $("#button-favicon").on('click', function(event) {
                        context = "favicon";
                    });

                    $("#button-logo").on('click', function(event) {
                        context = "logo";
                    });


                    $("#workspace").on('click', ".file", function(event) {

                        if (context == "logo") {

                            var src = $(event.target).attr('src');
                            var bname = src.substring(src.lastIndexOf('/') + 1);
                            $('[name="logo"]').val(bname);
                            $('#logo').attr('src', 'storage/images/logos/' + bname);

                        } else if (context == "favicon") {

                            var src = $(event.target).attr('src');
                            var bname = src.substring(src.lastIndexOf('/') + 1);
                            $('[name="favicon"]').val(bname);
                            $('#favicon').attr('src', 'storage/images/favicons/' + bname);

                        }

                        $('#close-modal').click();

                    });


                }
            </script>
        @endsection
