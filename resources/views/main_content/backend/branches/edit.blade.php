@extends('layouts.backend')

@section('pageTitle',_lang('app.edit'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('branches.index')}}">{{_lang('app.branches')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.edit')}}</span></li>
@endsection

@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>

<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>
<script src="{{url('public/backend/js')}}/branches.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditBranchesForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.title') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="{{ $branch->id }}">

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
            <h3 class="panel-title">{{_lang('app.location') }}</h3>
        </div>
        <div class="panel-body">


            <input value="{{ $branch->lat }}" type="hidden" id="lat" name="lat">
            <input value="{{ $branch->lng }}" type="hidden" id="lng" name="lng">
            <span class="help-block utbox"></span>
            <div class="maplarger">
                <input id="pac-input" class="controls" type="text"
                       placeholder="Enter a location">
                <div id="map" style="height: 500px; width:100%;"></div>
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
                    <input type="text" class="form-control" id="email" name="email" value="{{ $branch->email }}">
                    <label for="email">{{_lang('app.email') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="mobile" name="mobile" value="{{ $branch->mobile }}">
                    <label for="mobile">{{_lang('app.mobile') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="phone" name="phone" value="{{ $branch->phone }}">
                    <label for="phone">{{_lang('app.phone') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="{{ $branch->this_order }}">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="active" name="active">
                        <option {{ $branch->active == 1 ?'selected' : '' }} value="1">{{ _lang('app.active') }}</option>
                        <option {{ $branch->active == 0 ?'selected' : '' }} value="0">{{ _lang('app.not_active') }}</option>
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