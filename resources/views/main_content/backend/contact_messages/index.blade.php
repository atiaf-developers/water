@extends('layouts.backend')

@section('pageTitle', _lang('app.contact_messages'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>


<li><span> {{_lang('app.contact_messages')}}</span></li>


@endsection

@section('js')
<script src="{{url('public/backend/js')}}/contact_messages.js" type="text/javascript"></script>
@endsection
@section('content')
{{ csrf_field() }}
<div class="modal fade" id="viewMessage" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="viewMessageLabel"></h4>
            </div>

            <div class="modal-body">

                <p id="message">

                </p>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-white"
                        data-dismiss="modal"> {{ _lang("app.close") }} </button>
            </div>
        </div>
    </div>
</div>   
<div class="panel panel-default">
    <div class="panel-body">
        <!--Table Wrapper Start-->
        <div class="table-toolbar">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                        <a class="btn red-haze" href = "" onclick="Contact_messages.delete(this);
                                return false;">{{ _lang('app.delete')}}</a>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
            <thead>
                <tr>
                    <th>
                        <div class="md-checkbox col-md-4" style="margin-left:40%;">
                            <input type="checkbox" id="check-all-messages" class="md-check"  value="">
                            <label for="check-all-messages">
                                <span></span>
                                <span class="check"></span>
                                <span class="box"></span>
                            </label>
                        </div>
                    </th>
                    
                    <th>{{ _lang('app.type')}}</th>
                    <th>{{ _lang('app.name')}}</th>
                    <th>{{ _lang('app.email')}}</th>
                    <th>{{ _lang('app.created_at')}}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <!--Table Wrapper Finish-->
    </div>
</div>
<script>
    var new_lang = {
        
    };
</script>
@endsection
