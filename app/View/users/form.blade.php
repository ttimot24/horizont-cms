@extends('layout', ['title' => isset($user)? trans('user.edit_user') : trans('user.create_user')])

@section('content')
    <div class='container main-container'>

        <div class="card mb-3">

                    @include('breadcrumb', [
                        'links' => [['name' => trans('dashboard.content')], ['name' => trans('user.users'), 'url' => route('user.index')]],
                        'page_title' => trans(isset($user) ? 'user.edit_user' : 'user.create_user'),
                    ])
        
            <div class="card-body">

                <form role='form'
                    action="{{ isset($user) ? route('user.update', ['user' => $user]) : route('user.store') }}" method='POST'
                    enctype='multipart/form-data'>
                    @csrf

                    @if (isset($user)) @method('PUT') @endif

                    <div class="row">
                        <div class='form-group pull-left col-xs-12 col-md-8'>
                            <div class='form-group'>
                                <label for='name'>{{ trans('user.create_name') }}:</label>
                                <input type='text' class='form-control @error('name') is-invalid @enderror' id='name' name='name'
                                    value="{{ old('name', isset($user) ? $user->name : '') }}" required autofocus>
                            </div>

                            <div class='form-group'>
                                <label for='username'>{{ trans('user.create_username') }}:</label>
                                <input type='text' class='form-control' id='username' name='username'
                                    value="{{ old('username', isset($user) ? $user->username : '') }}"
                                    placeholder='Write username here' required>
                            </div>

                            @if (!isset($user) || ($user && $user->is($current_user)))
                                <div class='form-group'>
                                    <label for='title'>{{ trans('user.create_password') }}:</label>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>


                                <div class='form-group'>
                                    <label for='password'>{{ trans('user.create_password_again') }}:</label>

                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password_confirmation" required autocomplete="current-password">
                                </div>
                            @endif


                            <div class='form-group'>
                                <label for='title'>{{ trans('user.create_email') }}:</label>
                                <input type='email' class='form-control  @error('email') is-invalid @enderror' id='email' name='email'
                                    value="{{ old('email', isset($user) ? $user->email : '') }}" required>
                                
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class='form-group'>
                                <label for='title'>{{ trans('user.create_phone') }}:</label>
                                <input type='text' class='form-control' id='phone' name='phone'
                                    value="{{ old('phone', isset($user) ? $user->phone : '') }}">
                            </div>



                            <div class='form-group col-6'>
                                <label for='sel1'>{{ trans('user.create_select_rank') }}:</label>
                                <select class='form-select' name='role_id' id='sel1'>


                                    @foreach ($role_options as $each)
                                        @if ($each->permission <= $current_user->role->permission)
                                            <option value="{{ $each->id }}"
                                                @if (isset($user)) {{ $each->is($user->role) ? 'selected' : '' }}
                  @elseif(isset($settings['default_user_role']))
                    @if ($each->id == $settings['default_user_role']) selected @endif
                                                @endif
                                                >
                                                {{ $each->name }}
                                            </option>
                                        @endif
                                    @endforeach

                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">

                            @if (isset($user))
                                <button type='button' class='btn btn-link mb-5 w-100' data-bs-toggle='modal'
                                    data-bs-target='#modal-xl-{{ $user->id }}'>
                                    <img src='{{ $user->getThumb() }}' class='img img-thumbnail w-100'>
                                </button>
                            @endif

                            <div class='form-group'>
                                <label for='file'>{{ trans('actions.upload_image') }}:</label>
                                <input name='up_file' accept='image/*' id='input-2' type='file' class='file'
                                    data-max-file-size="{{ config('horizontcms.max_upload_file_size', 2560) }}KB"
                                    multiple='true' data-drop-zone-enabled="{{ isset($user) ? 'false' : 'true' }}"
                                    data-show-upload='false' data-show-caption='true'>


                                @error('up_file')
                                    <div class="text-danger" role="alert">
                                        <strong>{{ $errors->first('up_file') }}</strong>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class='form-group pull-left col-xs-12 col-md-8'>
                            <button id='submit-btn' type='submit'
                                class='btn btn-primary btn-lg'>{{ trans(isset($user) ? 'actions.update' : 'user.create_add_user_button') }}</button>
                        </div>

                </form>
            </div>
        </div>
    </div>

    @if (isset($user))
        @include('image_details', ['modal_id' => $user->id, 'image' => $user->getImageFilePath()])
    @endif
@endsection
