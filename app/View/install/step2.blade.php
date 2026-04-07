@extends('layout', ['title' => trans("Install")])

@section('content')
    <div class='jumbotron'>
        <div class='container'>
            <h1><small>Installing {{ config('app.name') }}</small></h1>




            <div class='progress'>
                <div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='40' aria-valuemin='0'
                    aria-valuemax='100' style='min-width: 40em;'>
                    40%
                </div>
            </div>
            <hr />

            <h2>Step 2: Database</h2>
            </br>


            @include('messages')

            </br>

            <form action='admin/install/check-connection' method='POST'>

                @csrf
                <div class='container'>

                    <div class='form-group'>
                        <label class='form-label col-md-2' for='server'>Database Driver</label>
                        <div class='col-md-5'>
                            <select class='form-select' name='db_driver'>

                                @foreach ($db_drivers as $driver => $alias)
                                    <option value="{{ $alias }}" {{ old('db_driver') == $alias ? 'selected' : '' }}>
                                        {{ $driver }}</option>
                                @endforeach

                            </select>

                        </div>
                    </div>

                    <div class='form-group'>
                        <label class='form-label col-sm-2' for='server'>Host</label>
                        <div class='col-sm-5'>
                            <input type='text' class='form-control' id='server' name='server'
                                value="{{ old('server', 'localhost') }}" required>
                        </div>
                    </div>

                    <div class='form-group'>
                        <label class='form-label col-sm-2' for='username'>Username</label>
                        <div class='col-sm-5'>
                            <input type='text' class='form-control' id='username' name='username' placeholder='username'
                                value="{{ old('username') }}" required autofocus>
                        </div>
                    </div>


                    <div class='form-group'>
                        <label class='form-label col-sm-2' for='pwd'>Password</label>
                        <div class='col-sm-5'>
                            <input type='password' class='form-control' id='pwd' name='password'
                                placeholder='password' value=''>
                        </div>
                    </div>

                    <div class='form-group'>
                        <label class='form-label col-sm-2' for='data'>Database</label>
                        <div class='col-sm-5'>
                            <input type='text' class='form-control' id='data' name='database'
                                placeholder='database name' value="{{ old('database') }}" required>
                        </div>
                    </div>

                    <div class='form-group'>
                        <label class='form-label col-sm-2' for='prefix'>Table prefix</label>
                        <div class='col-sm-5'>
                            <input type='text' class='form-control' id='prefix' name='prefix' placeholder='prefix'
                                value="{{ old('prefix', 'hcms_') }}">
                        </div>
                    </div>

                </div>
                <div class="pt-5">
                    <a href="{{route('install.show', 'step1')}}"><button type='button' class='btn btn-secondary btn-sm'><i
                                class="fa fa-arrow-circle-left" aria-hidden="true"></i> Previous</button></a>
                    <button type='submit' class='btn btn-primary btn-md px-3'>Next <i
                            class="fa fa-arrow-circle-right ml-2" aria-hidden="true"></i></button>
                </div>
            </form>

        </div>
    </div>
@endsection
