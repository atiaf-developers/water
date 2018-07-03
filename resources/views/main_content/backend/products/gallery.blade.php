@extends('layouts.backend')

@section('pageTitle', _lang('app.gallery'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/products')}}">{{_lang('app.products')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.gallery')}}</span></li>
@endsection

@section('js')
<script src="{{url('public/backend/js')}}/gallery.js" type="text/javascript"></script>
@endsection
@section('content')

<form id="galleryForm"  enctype="multipart/form-data">
    <div class="panel panel-default">
        <div class="panel-body">

            {{ csrf_field() }}
            <input type="hidden" name="id" id="id" value="{{$product->id}}">
            <div class="form-group col-sm-6 col-sm-offset-4">

                <div class="input-file">
                    <input type="file" name="gallery[]" id="gallery" multiple>
                    <span class="help-block"></span>
                </div>

            </div>

        </div>

    </div>

    <div class="progress" id="progress_div" style="display: none;">
        <div class="progress-bar" id="bar" role="progressbar" aria-valuenow="70"
             aria-valuemin="0" aria-valuemax="100" style="width:0%">
            <div id="percent"></div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="uploadedPics">
                @if(count($product->gallery)>0)
                @foreach($product->gallery as $index=> $one)
                @php $_id='image'.($index+1) @endphp
         
                <div class="classifiedUploadedPicContainer" style="margin-bottom: 10px;">
                    <input type="hidden" name="uploaded[]" value="{{$one}}">

                    <div class="classifiedUploadedPic" style="background-image:url({{url('public/uploads/products/'.$one)}});"></div> 
                    @if($lang_code=='ar')
                    <div class="classifiedUploadedPicTools">
                        <button class="moveClassifiedUploadedPicRight" type="button"><i class="fa fa-arrow-right"></i></button>
                        <button class="deleteClassifiedUploadedPic" type="button"><i class="fa fa-trash-o"></i></button>
                        <button class="moveClassifiedUploadedPicLeft" type="button"><i class="fa fa-arrow-left"></i></button>
                    </div>
                    @else
                    <div class="classifiedUploadedPicTools">
                        <button class="moveClassifiedUploadedPicRight" type="button"><i class="fa fa-arrow-left"></i></button>
                        <button class="deleteClassifiedUploadedPic" type="button"><i class="fa fa-trash-o"></i></button>
                        <button class="moveClassifiedUploadedPicLeft" type="button"><i class="fa fa-arrow-right"></i></button>
                    </div>
                    @endif
                </div>
                <!--        <div class="img-box"  style="position:relative;float:right;padding: 5px 5px;">
                            <img style="height:80px;width:80px;" src="{{url('public/uploads/products/'.$one)}}"/>
                            <div style="position: absolute; top: 4px; left: 4px; width: 15px; height: 15px; text-align: center; line-height: 15px; border-radius: 50px;">
                                <div class="md-checkbox">
                                    <input type="hidden" name="uploaded[]" value="{{$one}}">
                                    <input type="checkbox"  id="{{$_id}}"  value="{{$index}}" class="gallery_image md-check">
                                    <label for="{{$_id}}">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box" style="background-color: #fff;border: 2px solid #888;"></span></label>
                                </div>
                            </div>
                        </div>-->
                @endforeach
                @else
                <p class="text-center empty-message">{{_lang('app.no_images')}}</p>
                @endif

            </div>

        </div>
        <div class="panel-footer text-center">
            <button type="button" class="btn btn-info submit-form">{{_lang('app.save')}}</button>
        </div>
    </div>


</form>

<script>
var new_lang = {
    no_images:"{{_lang('app.no_images')}}"
};
var new_config = {
    upload_url: "{{url('admin/products/upload')}}"
};
</script>
@endsection