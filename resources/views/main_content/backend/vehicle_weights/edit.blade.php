@extends('layouts.backend')

@section('pageTitle',_lang('app.edit'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('vehicle_weights.index')}}">{{_lang('app.vehicle_weights')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.edit')}}</span></li>
@endsection

@section('js')
<script src="{{url('public/backend/js')}}/vehicle_weights.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditVehicleWeightsForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.title') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="{{ $vehicle_weight->id }}">

                @foreach ($languages as $key => $value)
                @php $_id='title_'.$key; @endphp
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="{{$_id}}" name="title[{{ $key }}]" value="{{  isset($translations["$key"]->title)?$translations["$key"]->title:'' }}">
                    <label for="{{$_id}}">{{ _lang('app.'.$value) }}</label>
                    <span class="help-block"></span>
                </div>

                @endforeach


            </div>
        </div>


    </div>


     


    <div class="panel panel-default">
       <div class="panel-heading">
                <h3 class="panel-title"></h3>
            </div>
        <div class="panel-body">

         
            <div class="form-body">
                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="{{ $vehicle_weight->this_order }}">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="active" name="active">
                        <option {{ $vehicle_weight->active == 1 ?'selected' : '' }} value="1">{{ _lang('app.active') }}</option>
                        <option {{ $vehicle_weight->active == 0 ?'selected' : '' }} value="0">{{ _lang('app.not_active') }}</option>
                    </select>
                     <label for="status">{{_lang('app.status') }}</label>
                    <span class="help-block"></span>
                </div> 

                <div class="clearfix"></div>

            </div>
        </div>

         <div class="panel-footer text-center">
            <button type="button" class="btn btn-info submit-form"
                    >{{_lang('app.save') }}</button>
        </div>


    </div>


</form>
<script>
var new_lang = {

};
var new_config = {

};

</script>
@endsection