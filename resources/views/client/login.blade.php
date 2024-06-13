<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="{{ asset('css/client-style.css') }}"  rel="stylesheet" >
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    

        <title>Login</title>
        <!-- Fonts -->
        {{--<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">--}}

        <!-- Styles -->

    </head>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?php $message = Session::get('message'); ?>
            @if( isset($message) )
                <div class="alert alert-success">{{$message}}</div>
            @endif
            @if($errors && ! $errors->isEmpty() )
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger">{{$error}}</div>
                @endforeach
            @endif
        </div>
    </div>
    <body class="login_bg">
    <div class="wrapper">

        <div class="login_form_container">
            <div class="login_screw_left"><img src="{{  asset('assets/images/staticImage/login_screw_left.png') }}" alt=""></div>
            <div class="login_screw_right"><img src="{{  asset('assets/images/staticImage/login_screw_right.png') }}" alt=""></div>
            <div class="login_screw_bottom_left"><img src="{{  asset('assets/images/staticImage/login_screw_left.png') }}" alt=""></div>
            <div class="login_screw_bottom_right"><img src="{{  asset('assets/images/staticImage/login_screw_right.png') }}" alt=""></div>
            <div class="logo">
                <img src="{{  asset('assets/images/staticImage/login_logo.png') }}" alt="">
            </div>
            <div class="login_form">

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group row">
                       
                        <div class="col-md-6">
                            <input id="email" autocomplete="off" type="email" placeholder="Email Address" class="form-control username {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                       
                        <div class="col-md-6">
                            <input id="password" type="password" placeholder="Password" class="form-control password {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6 offset-md-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Login') }}
                            </button>
                        </div>
                    </div>
                <a href="{{ url('/login') }}">Login as a Admin</a>
                </form>

            </div>
        </div>

        <footer class="footer"> <span>Cake software version 2.0.</span> </footer>

    </div>
    </body>
</html>
