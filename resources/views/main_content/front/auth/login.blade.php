@extends('layouts.front')

@section('pageTitle',_lang('app.login'))

@section('js')
<script src=" {{ url('public/front/scripts') }}/login.js"></script>
@endsection

@section('content')

<section id="login">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="title">
                    <h2>{{ _lang('app.login') }}</h2>
                </div>
                <div class="login-area">
                    <div class="form-w3agile margin">
                        <form class="contactus" id = "login-form">
                            <img class="user" src="{{ url('public/front/img') }}/male-user2.png" alt="" >
                            <p> <strong>{{ _lang('app.welcome') }},</strong> {{ _lang('app.login_to_continue') }}</p>

                            {{ csrf_field() }}


                            <div class="row">
                                <div class="form-group {{ $errors->has('username') ? ' has-error' : '' }}">
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="username" id="username" placeholder="{{ _lang('app.username') }}">
                                        <div class="valid-feedback help-block">

                                            @if ($errors->has('username'))
                                            {{ $errors->first('username') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                    <div class="col-md-12">
                                        <input type="password" name="password" id="password" class="form-control" placeholder="{{ _lang('app.password') }}">
                                        <div class="invalid-feedback help-block">
                                            @if ($errors->has('password'))
                                            {{ $errors->first('password') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--<a class="a-login" href="forget-password.php" >نسيت كلمة المرور؟</a>-->
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="#" class="button-login submit-form btn btn-lg">{{ _lang('app.login') }}</a>
                                </div>
                            </div>
                            <a class="a-signin border-top" href="{{ route('register') }}" >{{ _lang('app.register') }}</a>
                        </form>
                        <div class="clearfix"></div>
                        <div class="alert alert-success" style="display:{{Session('successMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-check" aria-hidden="true"></i> <span class="message">{{Session::get('successMessage')}}</span></div>
                        <div class="alert alert-danger" style="display:{{Session('errorMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span class="message">{{Session::get('errorMessage')}}</span></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection