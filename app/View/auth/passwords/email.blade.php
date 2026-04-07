@extends('layout', [
    'title' => "Password reset"
])


<div class='jumbotron'>
    <div class='container my-5'>
        <div class="row">
            <div class="col-md-2 col-xs-12 text-center">
                <img src='{{ $admin_logo }}' width="150" alt="">
            </div>
            <div class="col-md-4 col-xs-12 text-center">
                <h1 class="pt-3">{{ $app_name }}</h1>
                <p><q><i>Closer to the web</i></q></p>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>
</div>


<!-- Main Content -->
@section('content')
    <div class="container">
        <br>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="panel panel-default" style="background:transparent;">
                    <div class="panel-heading text-white p-2" style="background-color:#1f1f1f;">Reset Password</div>
                    <div class="panel-body pt-3" style="color:black;">
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form class="form-horizontal" role="form" method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-12 form-label">E-Mail Address</label>

                                <div class="col-md-12">
                                    <input id="email" type="email" class="form-control" name="email"
                                        value="{{ old('email') }}" required>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary">
                                    Send Password Reset Link
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
