@extends('layouts.backend')

@section('pageTitle',_lang('app.add'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('products.index')}}">{{_lang('app.products')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.add')}}</span></li>
@endsection

@section('js')
<script src="{{url('public/backend/js')}}/products.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditProductsForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.title') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="0">

                @foreach ($languages as $key => $value)
                @php $_id='title_'.$key; @endphp
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="{{$_id}}" name="title[{{ $key }}]" value="">
                    <label for="{{$_id}}">{{ _lang('app.'.$value) }}</label>
                    <span class="help-block"></span>
                </div>

                @endforeach


            </div>
        </div>


    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.description') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="0">

                @foreach ($languages as $key => $value)
                @php $_id='description_'.$key; @endphp
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="{{$_id}}" name="description[{{ $key }}]" value="">
                    <label for="{{$_id}}">{{ _lang('app.'.$value) }}</label>
                    <span class="help-block"></span>
                </div>

                @endforeach


            </div>
        </div>


    </div>
 




    <div class="panel panel-default">
        <div class="panel-body">


            <div class="form-body">
                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="price" name="price" value="">
                    <label for="price">{{_lang('app.price') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="discount_price" name="discount_price" value="">
                    <label for="discount_price">{{_lang('app.discount_price') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="quantity" name="quantity" value="">
                    <label for="quantity">{{_lang('app.quantity') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-4">
                    <select class="form-control" id="category" name="category">
                        <option value = "">{{_lang('app.choose')}}</option>
                        @foreach ($categories as $key => $value)
                        <option value = "{{$value->id}}">{{$value->title}}</option>
                        @endforeach
                    </select>
                    <label for = "category">{{_lang('app.category')}}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="active" name="active">
                        <option  value="1">{{ _lang('app.active') }}</option>
                        <option  value="0">{{ _lang('app.not_active') }}</option>
                    </select>
                    <label for="status">{{_lang('app.status') }}</label>
                    <span class="help-block"></span>
                </div> 

                <div class="clearfix"></div>
                <div class="form-group col-md-6">
                    <label class="control-label">{{_lang('app.image')}}</label>

                    <div class="image_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="image" />
                    </div>
                    <input type="file" name="image" id="image" style="display:none;">     
                    <span class="help-block"></span>             
                </div>

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
    action: 'add'
};

</script>
@endsection