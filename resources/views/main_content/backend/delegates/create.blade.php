@extends('layouts.backend')

@section('pageTitle',_lang('app.add'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('delegates.index')}}">{{_lang('app.delegates')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.add')}}</span></li>
@endsection

@section('js')
<script src="{{url('public/backend/js')}}/delegates.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditDelegatesForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.vehicle_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="0">
                <div class="col-md-12">
                    <div class="col-sm-6">
                        <div class="row">
                            <label class="col-sm-12 inputbox utbox control-label">{{_lang('app.letters_in_english')}} </label>
                            <div class="col-sm-12">
                                <div class="form-group col-sm-2 inputbox ">
                                    <input type="text" class="form-control" name="letter_english[0]" value="">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group col-sm-2 inputbox">
                                    <input type="text" class="form-control" name="letter_english[1]" value="">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group col-sm-2 inputbox">
                                    <input type="text" class="form-control" name="letter_english[2]" value="">
                                    <span class="help-block"></span>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <label class="col-sm-12 inputbox utbox control-label">{{_lang('app.numbers_in_english')}} </label>
                            <div class="col-sm-12">
                                <div class="form-group col-sm-2 inputbox">
                                    <input type="text" class="form-control" name="num_english[0]" value="">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group col-sm-2 inputbox">
                                    <input type="text" class="form-control" name="num_english[1]" value="">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group col-sm-2 inputbox">
                                    <input type="text" class="form-control" name="num_english[2]" value="">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group col-sm-2 inputbox">
                                    <input type="text" class="form-control" name="num_english[3]" value="">
                                    <span class="help-block"></span>
                                </div>

                            </div>
                        </div>

                    </div>


                    <div class="clearfix"></div>
                    <div class="col-sm-6">
                        <div class="row">
                            <label class="col-sm-12 inputbox utbox control-label">{{_lang('app.letters_in_arabic')}} </label>
                            <div class="col-sm-12">
                                <div class="form-group col-sm-2 inputbox">
                                    <input type="text" class="form-control" name="letter_arabic[0]" value="">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group col-sm-2 inputbox">
                                    <input type="text" class="form-control" name="letter_arabic[1]" value="">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group col-sm-2 inputbox">
                                    <input type="text" class="form-control" name="letter_arabic[2]" value="">
                                    <span class="help-block"></span>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <label class="col-sm-12 inputbox utbox control-label">{{_lang('app.numbers_in_arabic')}} </label>
                            <div class="col-sm-12">
                                <div class="form-group col-sm-2 inputbox">
                                    <input type="text" class="form-control" name="num_arabic[0]" value="">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group col-sm-2 inputbox">
                                    <input type="text" class="form-control" name="num_arabic[1]" value="">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group col-sm-2 inputbox">
                                    <input type="text" class="form-control" name="num_arabic[2]" value="">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group col-sm-2 inputbox">
                                    <input type="text" class="form-control" name="num_arabic[3]" value="">
                                    <span class="help-block"></span>
                                </div>

                            </div>
                        </div>

                    </div>


                </div>

                <!--Table Wrapper Finish-->
            </div>
        </div>


    </div>


    <div class="panel panel-default">
         
        <div class="panel-body">
            <div class="form-body">

              <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="vehicle_type" name="vehicle_type">
                        <option  value="">{{ _lang('app.choose') }}</option>
                        @foreach ($vehicle_types as $type)
                            <option  value="{{ $type->id }}">{{ $type->title }}</option>
                        @endforeach
                        
                    </select>
                    <label for="vehicle_type">{{_lang('app.vehicle_type') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="vehicle_weight" name="vehicle_weight">
                        <option  value="">{{ _lang('app.choose') }}</option>
                        @foreach ($vehicle_weights as $weight)
                            <option  value="{{ $weight->id }}">{{ $weight->title }}</option>
                        @endforeach
                        
                    </select>
                    <label for="vehicle_weight">{{_lang('app.vehicle_weight') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="license_number" name="license_number" value="">
                    <label for="license_number">{{_lang('app.license_number') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="price" name="price" value="">
                    <label for="price">{{_lang('app.price') }}</label>
                    <span class="help-block"></span>
                </div>


                <div class="form-group col-md-3">
                    <label class="control-label">{{_lang('app.vehicle_image')}}</label>

                    <div class="vehicle_image_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="vehicle_image" />
                    </div>
                    <input type="file" name="vehicle_image" id="vehicle_image" style="display:none;">     
                    <span class="help-block"></span>             
                </div>

                <div class="form-group col-md-3">
                    <label class="control-label">{{_lang('app.license_image')}}</label>

                    <div class="license_image_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="license_image" />
                    </div>
                    <input type="file" name="license_image" id="license_image" style="display:none;">     
                    <span class="help-block"></span>             
                </div>

                <div class="clearfix"></div>

            </div>
        </div>

    </div>




    <div class="panel panel-default">
         <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.delegate_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <div class="form-group form-md-line-input col-md-4">
                    <input type="text" class="form-control" id="name" name="name" value="">
                    <label for="name">{{_lang('app.name') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-4">
                    <input type="text" class="form-control" id="username" name="username" value="">
                    <label for="username">{{_lang('app.username') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-4">
                    <input type="email" class="form-control" id="email" name="email" value="">
                    <label for="email">{{_lang('app.email') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-4">
                    <input type="text" class="form-control" id="mobile" name="mobile" value="">
                    <label for="mobile">{{_lang('app.mobile') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-4">
                    <input type="password" class="form-control" id="password" name="password" value="">
                    <label for="password">{{_lang('app.password') }}</label>
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
                <div class="form-group col-md-3">
                    <label class="control-label">{{_lang('app.image')}}</label>

                    <div class="image_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="image" />
                    </div>
                    <input type="file" name="image" id="image" style="display:none;">     
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
       action:'add'
   };

</script>
@endsection