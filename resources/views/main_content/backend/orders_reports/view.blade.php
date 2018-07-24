@extends('layouts.backend')

@section('pageTitle', _lang('app.orders_reports'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/orders_reports')}}">{{_lang('app.orders_reports')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.view')}}</span></li>
@endsection
@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>
<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>
<script src="{{url('public/backend/js')}}/orders_reports.js" type="text/javascript"></script>
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
                                <td>{{ _lang('app.order_no')}}</td>
                                <td>{{$order->id}}</td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.client')}}</td>
                                <td><a href="{{url('admin/clients/'.$order->clientId)}}">{{$order->clientName}}</a></td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.driver')}}</td>
                                <td><a href="{{url('admin/drivers/'.$order->driverId)}}">{{$order->driverName}}</a></td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.driver_value')}}</td>
                                <td>{{$order->driverValue}}</td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.company_value')}}</td>
                                <td>{{$order->companyValue}}</td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.payment_method')}}</td>
                                <td>{{$order->paymentMethodText}}</td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.created_at')}}</td>
                                <td>{{$order->createdAt}}</td>

                            </tr>
                            <tr>
                                <td>{{ _lang('app.status')}}</td>
                                <td>{{$order->statusText}}</td>

                            </tr>
                            @if($order->rejectionReasonTitle)
                            <tr>
                                <td>{{ _lang('app.rejection_reason')}}</td>
                                <td>{{$order->rejectionReasonTitle}}</td>

                            </tr>
                            @endif
                            @if($order->rating)
                            <tr>
                                <td>{{ _lang('app.rating')}}</td>
                                <td>{{$order->rating}}</td>

                            </tr>
                            @endif
                      
                            <tr>
                                <td>{{ _lang('app.closed')}}</td>
                                <td>
                                    @php
                                    if ($order->closed==1) {
                                    $class = 'label-success';
                                    $message = _lang('app.is_closed');
                                    } else {
                                    $class = 'label-danger';
                                    $message = _lang('app.not_closed');
                                    }
                                    @endphp
                                    <span class="label label-sm {{ $class}}">
                                        {{$message}} </span>
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
                    <i class="fa fa-cogs"></i>{{_lang('app.location')}}
                </div>
            </div>
            <div class="portlet-body">

                <div class="table-scrollable">
                    <table class="table table-hover text-center">

                        <tbody>
                          
                            <tr>
                               
                                <td colspan="2">
                                    <input type="hidden" name="lat" id="lat" value="{{ $order->lat}}">
                                    <input type="hidden" name="lng" id="lng" value="{{ $order->lng }}">
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
