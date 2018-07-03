@extends('layouts.profile')

@section('pageTitle',_lang('app.edit_my_profile'))


@section('js')
<script src=" {{ url('public/front/scripts') }}/profile.js"></script>
@endsection

@section('content')
<div class="form-w3agile margin">
    <form class="contactus" novalidate id="edit-form" method="post" action="{{_url('custo        mer/user/edit')}}">
        {{ csrf_field() }}

        <div class="row">
            <div class="form-group  {{ $errors->has('name') ? ' has-error' : '' }}">
                <div class="col-md-12">
                    <div class="col-sm-9">
                        <div class="row">
                            <input type="text" name="name" class="form-control" value="{{$User->name}}">
                            <span class="help-block">
                                @if ($errors->has('name'))
                                {{ $errors->first('name') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <label class="col-sm-3 col-form-label">تعديل الاسم</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group  {{ $errors->has('username') ? ' has-error' : '' }}">
                <div class="col-md-12">
                    <div class="col-sm-9">
                        <div class="row">
                            <input type="text" name="username" class="form-control" value="{{$User->username}}">
                            <span class="help-block">
                                @if ($errors->has('username'))
                                {{ $errors->first('username') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <label class="col-sm-3 col-form-label">تعديل اسم المستخدم</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group  {{ $errors->has('email') ? ' has-error' : '' }}">
                <div class="col-md-12">
                    <div class="col-sm-9">
                        <div class="row">
                            <input type="text" name="email" class="form-control" value="{{$User->email}}">
                            <span class="help-block">
                                @if ($errors->has('email'))
                                {{ $errors->first('email') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <label class="col-sm-3 col-form-label">تعديل البريد الالكترونى</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group  {{ $errors->has('mobile') ? ' has-error' : '' }}">
                <div class="col-md-12">
                    <div class="col-sm-9">
                        <div class="row">
                            <input type="text" name="mobile" class="form-control" value="{{$User->mobile}}">
                            <span class="help-block">
                                @if ($errors->has('mobile'))
                                {{ $errors->first('mobile') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <label class="col-sm-3 col-form-label">رقم الجوال</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group  {{ $errors->has('password') ? ' has-error' : '' }}">
                <div class="col-md-12">
                    <div class="col-sm-9">
                        <div class="row">
                            <input type="password" name="password" class="form-control" placeholder="">
                            <span class="help-block">
                                @if ($errors->has('password'))
                                {{ $errors->first('password') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <label class="col-sm-3 col-form-label">تعديل كلمة المرور</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group  {{ $errors->has('confirm_password') ? ' has-error' : '' }}">
                <div class="col-md-12">
                    <div class="col-sm-9">
                        <div class="row">
                            <input type="password" name="confirm_password" class="form-control" placeholder="">
                            <span class="help-block">
                                @if ($errors->has('confirm_password'))
                                {{ $errors->first('confirm_password') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <label class="col-sm-3 col-form-label">اعادة كلمة المرور الجديدة</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group  {{ $errors->has('iamge') ? ' has-error' : '' }}">
                <div class="col-md-12">
                    <div class="col-sm-9">
                        <div class="row">
                            <input  style="padding-top: 20px;" type="file" name="image">
                        </div>
                    </div>
                    <label class="col-sm-3 col-form-label">تغيير الصورة الشخصية</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button class="button-login btn btn-lg save submit-form">حفظ</button>
            </div>
        </div>
    </form>
    <div class="alert alert-success" style="display:{{Session('successMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-check" aria-hidden="true"></i> <span class="message">{{Session::get('successMessage')}}</span></div>
    <div class="alert alert-danger" style="display:{{Session('errorMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span class="message">{{Session::get('errorMessage')}}</span></div>
</div>


@endsection