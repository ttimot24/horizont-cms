@extends('auth2.outer', ['title' => 'Welcome'])

@section('content-outer')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9 col-xl-8">

                <div class="card border-0 shadow-lg py-5">
                    <div class="card-body p-4 p-md-5">

                        @isset($admin_logo)
                            <div class="text-center mb-5 d-md-none">
                                <img src="{{ $admin_logo }}" class="img-fluid rounded" width="260" alt="Logo">
                            </div>
                        @endisset

                        <h1 class="text-center mb-4">Hello!</h1>

                        <form action="{{ route('login') }}" method="POST">
                            @csrf

                            <div class="row gy-4">

                                <div class="col-12">
                                    <label for="email" class="form-label">{{ trans('login.email') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        id="email" name="email" placeholder="{{ trans('login.enter_email') }}"
                                        required autofocus>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $errors->first('email') }}</span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="password" class="form-label">{{ trans('login.password') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="{{ trans('login.enter_password') }}"
                                        required>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $errors->first('password') }}</span>
                                    @enderror
                                </div>

                                <!--<div class="col-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me">
                                                        <label class="form-check-label" for="remember_me">
                                                            Maradjak bejelentkezve
                                                        </label>
                                                    </div>
                                                </div>-->

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        {{ trans('login.login') }}
                                    </button>
                                </div>

                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <a href="{{ route('password.request') }}" class="text-secondary text-decoration-none">
                                {{ trans('login.forgot_password') }}
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
