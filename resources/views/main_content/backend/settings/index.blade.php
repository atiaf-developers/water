@extends('layouts.backend')

@section('pageTitle',_lang('app.settings'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.settings')}}</span></li>

@endsection

@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>

<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>
<script src="{{url('public/backend/js')}}/settings.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="editSettingsForm"  enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="panel panel-default" id="editSiteSettings">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">

                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="phone" name="setting[phone]" value="{{isset($settings['phone'])?$settings['phone']->value:''}}">
                    <label for="phone">{{_lang('app.phone') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="email" name="setting[email]" value="{{isset($settings['email'])?$settings['email']->value:''}}">
                    <label for="email">{{_lang('app.email') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="commission" name="setting[commission]" value="{{isset($settings['commission'])?$settings['commission']->value:''}}">
                    <label for="commission">{{_lang('app.commission') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="email" name="setting[map_range]" value="{{isset($settings['map_range'])?$settings['map_range']->value:''}}">
                    <label for="map_range">{{_lang('app.map_range') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="email" name="setting[tax]" value="{{isset($settings['tax'])?$settings['tax']->value:''}}">
                    <label for="tax">{{_lang('app.tax') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="email" name="setting[delivery_cost]" value="{{isset($settings['delivery_cost'])?$settings['delivery_cost']->value:''}}">
                    <label for="delivery_cost">{{_lang('app.delivery_cost') }}</label>
                    <span class="help-block"></span>
                </div>



                <div class="clearfix"></div>




            </div>




            <!--Table Wrapper Finish-->
        </div>

    </div>
    <div class="panel panel-default" id="editSiteSettings">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.about') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">

                @foreach ($languages as $key => $value)
                <div class="panel panel-default">
       
                    <div class="panel-body">

                        <div class="form-body">
                            <div class="col-md-12">
                                <div class="form-group form-md-line-input">
                                    <textarea class="form-control" id="about[{{ $key }}]" name="setting[about][{{ $key }}]"  cols="30" rows="10">
                                        {{isset($settings['about']->value[$key])?$settings['about'][$key]->value[$key]:''}}
                                    </textarea>
                                    <label for="about">{{ _lang('app.'.$value) }}</label>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>




                        <!--Table Wrapper Finish-->
                    </div>

                </div>
                @endforeach



                <div class="clearfix"></div>




            </div>




            <!--Table Wrapper Finish-->
        </div>
        <div class="panel-footer text-center">
            <button type="button" class="btn btn-info submit-form"
                    >{{_lang('app.save') }}</button>
        </div>

    </div>

</form>
@endsection