@extends('layouts.backend')

@section('pageTitle', _lang('app.clients'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/clients')}}">{{_lang('app.clients')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.view')}}</span></li>
@endsection
@section('js')
<script src="{{url('public/backend/js')}}/app_delegates.js" type="text/javascript"></script>
@endsection
@section('content')
<div class="container">
    <div class="row">

        <h2 class="text-center"></h2>

        <div class="col-md-12">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet box red">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>{{_lang('app.info')}}
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse" data-original-title="" title="">
                        </a>

                        <a href="javascript:;" class="remove" data-original-title="" title="">
                        </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-hover text-center">

                            <tbody>
                                <tr>
                                    <td>{{ _lang('app.name')}}</td>
                                    <td>{{$user->name}}</td>

                                </tr>
                                <tr>
                                    <td>{{ _lang('app.mobile')}}</td>
                                    <td>{{$user->mobile}}</td>

                                </tr>
                                <tr>
                                    <td>{{ _lang('app.email')}}</td>
                                    <td>{{$user->email}}</td>

                                </tr>
                                                                <tr>
                                    <td>{{ _lang('app.status')}}</td>
                                    <td>
                                        @php
                                        if ($user->active==1) {
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
                                        <a class="fancybox-button" data-rel="fancybox-button" href="{{url('public/uploads/users/'.$user->image)}}">
                                            <img alt="" style="width:120px;height: 120px;" src="{{url('public/uploads/users/'.$user->image)}}">
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
        



    </div>
</div>
<script>
var new_lang = {

};

</script>
@endsection
