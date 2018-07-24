@extends('layouts.backend')

@section('pageTitle', _lang('app.orders_reports'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.orders_reports')}}</span></li>
@endsection

@section('js')

<script src="{{url('public/backend/js')}}/orders_reports.js" type="text/javascript"></script>
@endsection
@section('content')

<form method="" id="orders-reports">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{ _lang('app.filter') }}</h3>

        </div>

        <div class="panel-body">

            <div class="row">


                <div class="row">
                    <div class="form-group col-md-3">
                        <label class="col-sm-3 inputbox utbox control-label">{{ _lang('app.from') }}</label>
                        <div class="col-sm-9 inputbox">

                            <input type="date" class="form-control" placeholder=""  name="from" value="{{ (isset($from)) ? $from :'' }}">

                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="col-sm-3 inputbox utbox control-label">{{ _lang('app.to') }}</label>
                        <div class="col-sm-9 inputbox">

                            <input type="date" class="form-control" placeholder=""  name="to" value="{{ (isset($to)) ? $to :'' }}">

                        </div>
                    </div>
                    
                    <div class="form-group col-sm-3">
                        <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.status')}}</label>
                        <div class="col-sm-9 inputbox">
                            <select class="form-control" name="status" id="status">
                                <option value="">{{_lang('app.choose')}}</option>
                                @foreach($status_arr as $one)
                                <option {{ (isset($status) && $status==$one['status_no']) ?'selected':''}} value="{{$one['status_no']}}">{{_lang('app.'.$one['admin_message'])}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label class="col-sm-3 inputbox utbox control-label">{{ _lang('app.order_id') }}</label>
                        <div class="col-sm-9 inputbox">

                            <input type="text" class="form-control" placeholder=""  name="order" value="{{ (isset($order)) ? $order :'' }}">

                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="col-sm-3 inputbox utbox control-label">{{ _lang('app.client_id') }}</label>
                        <div class="col-sm-9 inputbox">

                            <input type="text" class="form-control" placeholder=""  name="client" value="{{ (isset($client)) ? $client :'' }}">

                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="col-sm-3 inputbox utbox control-label">{{ _lang('app.driver_id') }}</label>
                        <div class="col-sm-9 inputbox">

                            <input type="text" class="form-control" placeholder=""  name="driver" value="{{ (isset($driver)) ? $driver :'' }}">

                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button class="btn btn-info submit-form btn-report" type="submit">{{ _lang('app.apply') }}</button>
                    </div>
                </div>










            </div>
            <!--row-->
        </div>

    </div>
</form>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ _lang('app.results') }}</h3>

    </div>
    <div class="panel-body">


        <div class="row">
            @if($orders->count()>0)
            <div class="col-sm-12">
                <table class = "table table-responsive table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{_lang('app.order_no')}}</th>
                            <th>{{_lang('app.client')}}</th>
                            <th>{{_lang('app.driver')}}</th>
                            <th>{{_lang('app.total_price')}}</th>


                            <th>{{_lang('app.payment_method')}}</th>
                            <th>{{_lang('app.status')}}</th>
                            <th colspan="2">{{_lang('app.created_at')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $one)
                        <tr>
                            <td>{{$one->id}}</td>
                            <td>{{$one->clientName}}</td>
                            <td>{{$one->driverName}}</td>
                            <td>{{$one->totalPrice}}</td>
                            <td>{{$one->paymentMethodText}}</td>
                            <td>{{$one->statusText}}</td>
                            <td>{{$one->createdAt}}</td>
                            <td>
                                <a class="btn btn-xs" href="{{url('admin/orders_reports/'.$one->id)}}">{{_lang('app.view')}}</a>
                                @if($one->closed==0)
                                <a class="btn red-sunglo btn-xs" onclick = "Orders.closed(this);return false;" data-id="{{$one->id}}" href="">{{_lang('app.close')}}</a>
                                @endif
                                @if($one->closed==1)
                                <a class="btn green-meadow btn-xs" onclick = "Orders.closed(this);return false;" data-id="{{$one->id}}" href="">{{_lang('app.open')}}</a>
                                @endif
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-center">

                            <td colspan="4">{{_lang('app.total_price')}}</td>
                            <td colspan="4">{{$info->totalPrice}}</td>

                        </tr>
                        <tr class="text-center">

                            <td colspan="4">{{_lang('app.company_value')}}</td>
                            <td colspan="4">{{$info->companyValue}}</td>

                        </tr>




                    </tfoot>
                </table>
            </div>
            <div class="text-center">
                {{ $orders->links() }}  
            </div>
            @else
            <p class="text-center">{{_lang('app.no_results')}}</p>
            @endif


        </div>
        <!--row-->
    </div>

</div>

<script>
    var new_lang = {

    };
</script>
@endsection