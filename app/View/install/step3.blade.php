@extends('layout', ['title' => trans("Install")])

@section('content')
    <div class='jumbotron'>
        <div class='container'>
            <h1><small>Installing {{ config('app.name') }}</small></h1>


            <div class='progress'>
                <div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='80' aria-valuemin='0'
                    aria-valuemax='100' style='min-width: 80em;'>
                    80%
                </div>
            </div>
            <hr />


            <h2>Step 3: Administrator</h2>
            </br>

            @include('messages')

            </br>

            <form action="{{route('install.show', 'migrate')}}" method='POST'>

                @csrf

                <div class='container'>

                    <div class='form-group'>
                        <label class='form-label col-sm-3' for='username'>Create username</label>
                        <div class='col-sm-5'>
                            <input type='text' class='form-control' id='username' name='ad_username'
                                placeholder='username' value='<?= @$data['session']['ad_username'] ?>' required>
                        </div>
                    </div>

                    <div class='form-group'>
                        <label class='form-label col-sm-3' for='pwd'>Create password</label>
                        <div class='col-sm-5'>
                            <input type='password' class='form-control' id='pwd' name='ad_password'
                                placeholder='password' value='<?= @$data['session']['ad_password'] ?>' required>
                        </div>
                    </div>

                    <div class='form-group'>
                        <label class='form-label col-sm-3' for='em'>Email</label>
                        <div class='col-sm-5'>
                            <input type='email' class='form-control' id='em' name='ad_email' placeholder='email'
                                value='<?= @$data['session']['ad_email'] ?>' required>
                        </div>
                    </div>
                </div>

                <div class="pt-5">
                    <a href="{{route('install.show', 'step2')}}"><button type='button' class='btn btn-secondary btn-sm'><i
                                class="fa fa-arrow-circle-left" aria-hidden="true"></i> Previous</button></a>
                    <button type='submit' class='btn btn-primary btn-md px-3'>Next <i
                            class="fa fa-arrow-circle-right ml-2" aria-hidden="true"></i></button>
                </div>
        </div>
        </form>

    </div>
    </div>
@endsection
