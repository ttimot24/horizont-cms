@extends('auth2.outer', [
    'title' => "Password reset"
])

@section('content-outer')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9 col-xl-8">

            <div class="card border-0 shadow-lg py-5">
                <div class="card-body p-4 p-md-5">

                    <h1 class="text-center mb-4">Reset Password</h1>

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <!-- Email -->
                        <div class="my-5">
                            <label for="email" class="form-label">E-Mail Address <span class="text-danger">*</span></label>
                            <input type="email"
                                class="form-control form-control-lg @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter your email"
                                required>

                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Send Password Reset Link
                            </button>
                        </div>

                    </form>

                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class="text-secondary text-decoration-none">
                                {{ trans('Back to Login') }}
                            </a>
                        </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection