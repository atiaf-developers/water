@extends('layouts.backend')

@section('pageTitle', _lang('app.drivers'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/drivers')}}">{{_lang('app.drivers')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.view')}}</span></li>
@endsection
@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>
<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>
<script src="{{url('public/backend/js')}}/drivers.js" type="text/javascript"></script>
@endsection
@section('content')

<div class="row">

    <h2 class="text-center"></h2>

    <div class="col-md-4">
        <!-- BEGIN SAMPLE TABLE PORTLET-->
        <div class="portlet box red">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs"></i>{{_lang('app.vehicle')}}
                </div>
            </div>
            <div class="portlet-body">

                <div class="table-scrollable">
                    <table class="table table-hover text-center">

                        <tbody>
                            <tr>
                                <td>{{ _lang('app.plate_letter')}}</td>
                                @php $plate_letter='plate_letter_'.$lang_code @endphp
                                <td>{{$vehicle->$plate_letter}}</td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.plate_num')}}</td>
                                @php $plate_num='plate_num_'.$lang_code @endphp
                                <td>{{$vehicle->$plate_num}}</td>

                            </tr>

                            <tr>
                                <td>{{ _lang('app.email')}}</td>
                                <td>{{$vehicle->email}}</td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.rating')}}</td>
                                <td>{{$vehicle->rating}}</td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.total_rates')}}</td>
                                <td>{{$vehicle->total_rates?$vehicle->total_rates:0}}</td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.status')}}</td>
                                <td>
                                    @php
                                    if ($vehicle->is_ready==1) {
                                    $class = 'label-success';
                                    $message = _lang('app.is_ready_now');
                                    } else {
                                    $class = 'label-danger';
                                    $message = _lang('app.not_ready_now');
                                    }
                                    @endphp
                                    <span class="label label-sm {{ $class}}">
                                        {{$message}} </span>
                                </td>
                            </tr>


                            <tr>
                                <td>{{ _lang('app.vehicle_image')}}</td>
                                <td>
                                    <a class="fancybox-button" data-rel="fancybox-button" href="{{url('public/uploads/vehicles/'.$vehicle->vehicle_image)}}">
                                        <img alt="" style="width:80px;height: 80px;" src="{{url('public/uploads/vehicles/'.$vehicle->vehicle_image)}}">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ _lang('app.license_image')}}</td>
                                <td>
                                    <a class="fancybox-button" data-rel="fancybox-button" href="{{url('public/uploads/vehicles/'.$vehicle->license_image)}}">
                                        <img alt="" style="width:80px;height: 80px;" src="{{url('public/uploads/vehicles/'.$vehicle->license_image)}}">
                                    </a>
                                </td>
                            </tr>
                            










                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END SAMPLE TABLE PORTLET-->
    </div>
    <div class="col-md-6">
        <!-- BEGIN SAMPLE TABLE PORTLET-->
        <div class="portlet box red">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs"></i>{{_lang('app.driver')}}
                </div>
            </div>
            <div class="portlet-body">

                <div class="table-scrollable">
                    <table class="table table-hover text-center">

                        <tbody>
                            <tr>
                                <td>{{ _lang('app.name')}}</td>
                                <td>{{$vehicle->name}}</td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.username')}}</td>
                                <td>{{$vehicle->username}}</td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.mobile')}}</td>
                                <td>{{$vehicle->mobile}}</td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.email')}}</td>
                                <td>{{$vehicle->email}}</td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.status')}}</td>
                                <td>
                                    @php
                                    if ($vehicle->active==1) {
                                    $class = 'label-success';
                                    $message = _lang('app.active');
                                    } else {
                                    $class = 'label-danger';
                                    $message = _lang('app.not_active');
                                    }
                                    @endphp
                                    <span class="label label-sm {{ $class}}">
                                        {{$message}} </span>
                                </td>
                            </tr>


                            <tr>
                                <td>{{ _lang('app.image')}}</td>
                                <td>
                                    <a class="fancybox-button" data-rel="fancybox-button" href="{{url('public/uploads/users/'.$vehicle->driver_image)}}">
                                        <img alt="" style="width:120px;height: 120px;" src="{{url('public/uploads/users/'.$vehicle->driver_image)}}">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                               
                                <td colspan="2">
                                    <input type="hidden" name="lat" id="lat" value="{{ $vehicle->lat}}">
                                    <input type="hidden" name="lng" id="lng" value="{{ $vehicle->lng }}">
                                    <div id="map" style="height: 300px; width:100%;"></div>
                                </td>
                            </tr>










                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END SAMPLE TABLE PORTLET-->
    </div>
   




</div>

<script>
var new_lang = {
};
var new_config = {
    action: 'view'
};

</script>
@endsection
