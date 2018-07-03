@extends('layouts.backend')

@section('pageTitle', _lang('app.orders'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.orders')}}</span></li>
@endsection

@section('js')

<script src="{{url('public/backend/js')}}/orders_reports.js" type="text/javascript"></script>
@endsection
@section('content')

<form method="" id="orders-reports">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{ _lang('app.filter_by') }}</h3>
        </div>
        <div class="panel-body">

            <div class="row">


                <div class="row">
                    <div class="form-group col-md-4 col-md-offset-1">
                        <label class="col-sm-3 inputbox utbox control-label">{{ _lang('app.from') }}</label>
                        <div class="col-sm-9 inputbox">

                            <input type="date" class="form-control" placeholder=""  name="from" value="{{ (isset($from)) ? $from :'' }}">

                        </div>
                    </div>
                    <div class="form-group col-md-4 col-md-offset-1">
                        <label class="col-sm-3 inputbox utbox control-label">{{ _lang('app.to') }}</label>
                        <div class="col-sm-9 inputbox">

                            <input type="date" class="form-control" placeholder=""  name="to" value="{{ (isset($to)) ? $to :'' }}">

                        </div>
                    </div>


                </div>
                <div class="row">
             
                    <div class="form-group col-sm-4">
                        <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.users')}}</label>
                        <div class="col-sm-9 inputbox">
                            <select class="form-control" name="user" id="user">
                                <option value="">{{_lang('app.choose')}}</option>
                                @foreach($users as $one)
                                <option {{ (isset($user) && $user==$one->id) ?'selected':''}} value="{{$one->id}}">{{$one->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
              
                    <div class="form-group col-sm-4">
                        <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.branches')}}</label>
                        <div class="col-sm-9 inputbox">
                            <select class="form-control" name="branch" id="branch">
                                <option value="">{{_lang('app.choose')}}</option>
                                @foreach($branches as $one)
                                <option {{ (isset($branch) && $branch==$one->id) ?'selected':''}} value="{{$one->id}}">{{$one->title}}</option>
                                @endforeach


                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4 col-md-offset-1">
                        <label class="col-sm-3 inputbox utbox control-label">{{ _lang('app.order_no') }}</label>
                        <div class="col-sm-9 inputbox">

                            <input type="text" class="form-control" placeholder=""  name="order" value="{{ (isset($order)) ? $order :'' }}">

                        </div>
                    </div>


                </div>









            </div>
            <!--row-->
        </div>
        <div class="panel-footer text-center">
            <button class="btn btn-info submit-form btn-report" type="submit">{{ _lang('app.apply') }}</button>
        </div>
    </div>
</form>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-left">
            <h3 class="panel-title">{{ _lang('app.search_results') }}</h3>
        </div>
    
        <div class="clearfix"></div>
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
                            <th>{{_lang('app.branch')}}</th>
                            <th>{{_lang('app.total_price')}}</th>


                            <th>{{_lang('app.payment_method')}}</th>
                            <th colspan="2">{{_lang('app.created_at')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $one)
                        <tr>
                            <td>{{$one->id}}</td>
                            <td>{{$one->client_name}}</td>
                            <td>{{$one->branch_title}}</td>
                            <td>{{$one->total_price}}</td>
                            <td>{{$one->payment_method==1?_lang('app.cash_on_delivery'):_lang('app.visa')}}</td>
                            <td>{{$one->created_at}}</td>
                            <td><a class="btn btn-sm btn-info" href="{{url('admin/orders_reports/'.$one->id)}}">{{_lang('app.details')}}</a></td>

                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-center">

                            <td colspan="4">{{_lang('app.total_cost')}}</td>
                            <td colspan="4">{{$info->total_price}}</td>

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