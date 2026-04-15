@extends('auth2.outer', [
    'title' => "Forgot password"
])

@section('content-outer')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9 col-xl-8">

            <div class="card border-0 shadow-lg py-5">
                <div class="card-body p-4 p-md-5">

                    <h1 class="text-center mb-4">Reset Password</h1>

                    <form method="POST" action="{{ route('password.request') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label">E-Mail Address <span class="text-danger">*</span></label>
                            <input type="email"
                                class="form-control form-control-lg @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email', isset($email) ? $email : '') }}"
                                placeholder="Enter your email"
                                required
                                autofocus>

                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password"
                                pattern=".{6,}"
                                class="form-control form-control-lg @error('password') is-invalid @enderror"
                                id="password"
                                name="password"
                                placeholder="Enter new password"
                                required>

                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password-confirm" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password"
                                pattern=".{6,}"
                                class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                                id="password-confirm"
                                name="password_confirmation"
                                placeholder="Repeat new password"
                                required>

                            @error('password_confirmation')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Reset Password
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection