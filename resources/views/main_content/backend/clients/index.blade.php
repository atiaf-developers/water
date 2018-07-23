@extends('layouts.backend')

@section('pageTitle', _lang('app.clients'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.clients')}}</span></li>
@endsection
@section('js')
<script src="{{url('public/backend/js')}}/clients.js" type="text/javascript"></script>
@endsection
@section('content')
 {{ csrf_field() }}
<div class = "panel panel-default">
 
    <div class = "panel-body">
        <!--Table Wrapper Start-->

        <div class="table-container">

            <table class = "table table-responsive table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
                <thead>
                    <tr>
                        <th>{{_lang('app.id')}}</th>
                        <th width="20%;">{{_lang('app.name')}}</th>
                        <th>{{_lang('app.mobile')}}</th>
                        <th>{{_lang('app.email')}}</th>
                        <th>{{_lang('app.image')}}</th>
                        <th>{{_lang('app.status')}}</th>
                        <th>{{_lang('app.created_at')}}</th>
                        <th>{{_lang('app.options')}}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>  
        </div>




        <!--Table Wrapper Finish-->
    </div>
</div>
<script>
    var new_lang = {
   
    };
</script>
@endsection