@extends('auth2.outer', [
    'title' => 'Register'
])

@section('content-outer')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9 col-xl-8">

            <div class="card border-0 shadow-lg py-2">
                <div class="card-body p-4 p-md-5">

                    @isset($admin_logo)
                        <div class="text-center mb-5 d-md-none">
                            <img src="{{ $admin_logo }}" class="img-fluid rounded" width="260" alt="Logo">
                        </div>
                    @endisset

                    <h1 class="text-center mb-4">Sign Up</h1>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control form-control-lg @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Enter name"
                                required
                                autofocus>

                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="mb-4">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control form-control-lg @error('username') is-invalid @enderror"
                                id="username"
                                name="username"
                                value="{{ old('username') }}"
                                placeholder="Enter username"
                                required>

                            @error('username')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label">{{ trans('login.email') }} <span class="text-danger">*</span></label>
                            <input type="email"
                                class="form-control form-control-lg @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="{{ trans('login.enter_email') }}"
                                required>

                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password"
                                class="form-control form-control-lg @error('password') is-invalid @enderror"
                                id="password"
                                name="password"
                                placeholder="Enter password"
                                required>

                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password-confirm" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password"
                                class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                                id="password-confirm"
                                name="password_confirmation"
                                placeholder="Repeat password"
                                required>

                            @error('password_confirmation')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Register
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection