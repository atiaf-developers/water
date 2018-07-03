@extends('layouts.backend')

@section('pageTitle')
{{_lang('app.orders') }}
@endsection
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/orders_reports')}}">{{_lang('app.orders')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.view')}}</span></li>
@endsection
@section('js')
<script src="{{url('public/backend/js')}}/orders_reports.js" type="text/javascript"></script>
@endsection
@section('content')


<div class="row">
    <div class="col-md-12">
        <!-- Begin: life time stats -->
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-shopping-cart"></i>{{_lang('app.order')}}  <span class="hidden-480">
                        | {{ $order->created_at }} </span>
                </div>
                <div class="actions">
                    <a href="{{url('admin/orders_reports')}}" class="btn default yellow-stripe">
                        <i class="fa fa-angle-left"></i>
                        <span class="hidden-480">
                            {{  _lang('back') }} </span>
                    </a>
                    <a href="javascript:;" onclick="My.print('invoice-content')" class="btn default yellow-stripe">
                        <i class="fa fa-print" aria-hidden="true"></i>
                        <span class="hidden-480">
                            {{  _lang('print') }}</span>
                    </a>

                </div>
            </div>
            <div class="portlet-body">
                <div class="tabbable">
                    <ul class="nav nav-tabs nav-tabs-lg">
                        <li class="active">
                            <a href="#tab_1" data-toggle="tab" aria-expanded="true">
                                {{ _lang('app.detailes') }} </a>
                        </li>

                        <li class="">
                            <a href="#tab_2" data-toggle="tab" aria-expanded="true">
                                {{ _lang('app.reply') }} </a>
                        </li>



                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="portlet yellow-crusta box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>{{_lang('app.order_details')}} 
                                            </div>
                                            <!--                                            <div class="actions">
                                                                                            <a href="javascript:;" class="btn btn-default btn-sm"><span class="md-click-circle md-click-animate" style="height: 67px; width: 67px; top: -17.5px; left: 18.1406px;"></span>
                                                                                                <i class="fa fa-pencil"></i> Edit </a>
                                                                                        </div>-->
                                        </div>
                                        <div class="portlet-body">
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.username')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->client_name}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.order_no')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->id}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.branch')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->branch_title}}
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.payment_method')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->payment_method==1?_lang('app.cash_on_delivery'):_lang('app.visa')}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.total_price')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->total_price}}
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6 col-sm-12">
                                    <div class="portlet blue-hoki box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>{{_lang('app.address')}}
                                            </div>

                                        </div>
                                        <div class="portlet-body">
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.client_name')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->client_name}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.client_mobile')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->client_mobile}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.account_type')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->account_type_title}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.city')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->city}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.region')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->region}}
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.street')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->street}}
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.building_number')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->building}}
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>


                            </div>


                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="portlet grey-cascade box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>{{_lang('app.shopping_cart')}} 
                                            </div>

                                        </div>
                                        <div class="portlet-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th> {{_lang('app.no')}}</th>
                                                            <th> {{_lang('app.product')}}</th>
                                                            <th> {{_lang('app.price')}}</th>
                                                            <th> {{_lang('app.quantity')}}</th>
                                                            <th> {{_lang('app.total_price')}}</th>


                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($order->details as $key=> $one)
                                                        <tr>
                                                            <td> {{$key+1}} </td>
                                                            <td>
                                                                <h5>{{$one->title}}</h5>

                                                            </td>
                                                            <td>{{$one->price}}</td>
                                                            <td>{{$one->quantity}}</td>
                                                            <td>{{$one->total_price}}</td>
                                                        </tr>
                                                        @endforeach

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <div class="well">




                                        <div class="row static-info align-reverse">
                                            <div class="col-md-8 name">
                                                {{ _lang('app.total_price') }}
                                            </div>
                                            <div class="col-md-3 value">
                                                {{ $order->total_price }} {{ $currency_sign }}
                                            </div>
                                        </div>





                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab_2">

                            <div class="row">
                                <form action="" method="post" id="orderStatusForm">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="order_id" value="{{ Crypt::encrypt($order->id) }}">

                                    <div class="col-md-6">
                                        <div class="form-group form-md-line-input">
                                            <textarea rows="5" class="form-control" id="reply" name="reply">{{$order->reply?$order->reply:''}}</textarea>
                                            <label for="reply">{{_lang('app.reply') }}</label>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <button class="submit-form btn btn-sm yellow" type="submit"><i class="fa fa-check"></i> {{ _lang('app.save') }}</button>

                                    </div>


                                </form>



                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        <!-- End: life time stats -->
    </div>
</div>

<div id="invoice-content" style="display: none;"> 
    @include('reports/receipt')
</div>
<script>
    var new_lang = {

    };
    var new_config = {

    }

</script>
@endsection
