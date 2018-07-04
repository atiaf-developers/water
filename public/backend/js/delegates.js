var Delegates_grid;
var Delegates = function () {

    var init = function () {
        $.extend(lang, new_lang);
        $.extend(config, new_config);
        handleRecords();
        handleSubmit();
        My.readImageMulti('image');
        My.readImageMulti('vehicle_image');
        My.readImageMulti('license_image');
    };

    var handleRecords = function () {

        Delegates_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/delegates/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
            {"data": "name", "name": "users.name"},
            {"data": "image", orderable: false, searchable: false},
            {"data": "vehicle_image",  orderable: false, searchable: false},
            {"data": "active", orderable: false, searchable: false},
            {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
            [2, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {

        if ($('#addEditDelegatesForm').length > 0) {


            $('#addEditDelegatesForm').validate({
                rules: {
                    'letter_english[0]':{
                     required: true,
                 },
                 'letter_english[1]':{
                     required: true,
                 },
                 'letter_english[2]':{
                     required: true,
                 },
                 'num_english[0]':{
                     required: true,
                 },
                 'num_english[1]':{
                     required: true,
                 },
                 'num_english[2]':{
                     required: true,
                 },
                 'num_english[3]':{
                     required: true,
                 },

                 'letter_arabic[0]':{
                     required: true,
                 },
                 'letter_arabic[1]':{
                     required: true,
                 },
                 'letter_arabic[2]':{
                     required: true,
                 },
                 'num_arabic[0]':{
                     required: true,
                 },
                 'num_arabic[1]':{
                     required: true,
                 },
                 'num_arabic[2]':{
                     required: true,
                 },
                 'num_arabic[3]':{
                     required: true,
                 },
                 vehicle_type: {
                    required: true,
                },
                vehicle_weight: {
                    required: true,
                },
                license_number: {
                    required: true,
                },
                price: {
                    required: true,
                },
                name: {
                    required: true,
                },
                username: {
                    required: true,
                },
                email: {
                    email:true,
                    required: true,
                },
                mobile: {
                    required: true,
                },
                active: {
                    required: true,
                },


            },
                //messages: lang.messages,
                highlight: function (element) { // hightlight error inputs
                    $(element).closest('.form-group').removeClass('has-success').addClass('has-error');

                },
                unhighlight: function (element) {
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                    $(element).closest('.form-group').find('.help-block').html('').css('opacity', 0);

                },
                errorPlacement: function (error, element) {
                    $(element).closest('.form-group').find('.help-block').html($(error).html()).css('opacity', 1);
                }
            });
            var image = "input[name='image']";
            var vehicle_image = "input[name='vehicle_image']";
            var license_image = "input[name='license_image']";
            if (config.action == 'add') {
                $(image).rules('add', {
                    required: true,
                    accept: "image/*",
                    filesize: 1000 * 1024
                });
                $(vehicle_image).rules('add', {
                    required: true,
                    accept: "image/*",
                    filesize: 1000 * 1024
                });
                $(vehicle_image).rules('add', {
                    required: true,
                    accept: "image/*",
                    filesize: 1000 * 1024
                });

            } else {
                $(image).rules('add', {
                    accept: "image/*",
                    filesize: 1000 * 1024
                });
                 $(vehicle_image).rules('add', {
                    accept: "image/*",
                    filesize: 1000 * 1024
                });
                  $(vehicle_image).rules('add', {
                    accept: "image/*",
                    filesize: 1000 * 1024
                });
            }
            $('#addEditDelegatesForm .submit-form').click(function () {

                if ($('#addEditDelegatesForm').validate().form()) {
                    $('#addEditDelegatesForm .submit-form').prop('disabled', true);
                    $('#addEditDelegatesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditDelegatesForm').submit();
                    }, 1000);
                }
                return false;
            });
            $('#addEditDelegatesForm input').keypress(function (e) {
                if (e.which == 13) {
                    if ($('#addEditDelegatesForm').validate().form()) {
                        $('#addEditDelegatesForm .submit-form').prop('disabled', true);
                        $('#addEditDelegatesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                        setTimeout(function () {
                            $('#addEditDelegatesForm').submit();
                        }, 1000);
                    }
                    return false;
                }
            });



            $('#addEditDelegatesForm').submit(function () {
                var id = $('#id').val();
                var action = config.admin_url + '/delegates';
                var formData = new FormData($(this)[0]);
                if (id != 0) {
                    formData.append('_method', 'PATCH');
                    action = config.admin_url + '/delegates/' + id;
                }
                $.ajax({
                    url: action,
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        console.log(data);
                        $('#addEditDelegatesForm .submit-form').prop('disabled', false);
                        $('#addEditDelegatesForm .submit-form').html(lang.save);

                        if (data.type == 'success')
                        {
                            My.toast(data.message);
                            if (id == 0) {
                                Delegates.empty();
                            }


                        } else {
                            console.log(data)
                            if (typeof data.errors === 'object') {
                                for (i in data.errors) {
                                    var message = data.errors[i];
                                    if (i.startsWith('title') || i.startsWith('description') || i.startsWith('address') || i.startsWith('about')) {
                                        var key_arr = i.split('.');
                                        var key_text = key_arr[0] + '[' + key_arr[1] + ']';
                                        i = key_text;
                                    }
                                    $('[name="' + i + '"]')
                                    .closest('.form-group').addClass('has-error');
                                    $('#' + i).closest('.form-group').find(".help-block").html(message).css('opacity', 1)
                                }
                            }
                            if (typeof data.message !== 'undefined') {
                                $.confirm({
                                    title: lang.error,
                                    content: data.message,
                                    type: 'red',
                                    typeAnimated: true,
                                    buttons: {
                                        tryAgain: {
                                            text: lang.try_again,
                                            btnClass: 'btn-red',
                                            action: function() {}
                                        }
                                    }
                                });
                            }
                        }
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        $('#addEditDelegatesForm .submit-form').prop('disabled', false);
                        $('#addEditDelegatesForm .submit-form').html(lang.save);
                        My.ajax_error_message(xhr);
                    },
                    dataType: "json",
                    type: "POST"
                });


                return false;

            })

        }


    }

    return {
        init: function () {
            init();
        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/delegates/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    Delegates_grid.api().ajax.reload();
                }
            });

        },
        add: function () {
            Delegates.empty();
            My.setModalTitle('#addEditDelegates', lang.add);
            $('#addEditDelegates').modal('show');
        },
         status: function(t) {
            var delegate_id = $(t).data("id"); 
            $(t).prop('disabled', true);
            $(t).html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');

            $.ajax({
                url: config.admin_url+'/delegates/status/'+delegate_id,
                success: function(data){  
                     $(t).prop('disabled', false);
                     if ($(t).hasClass( "btn-info" )) {
                        $(t).addClass('btn-danger').removeClass('btn-info');
                        $(t).html(lang.not_active);

                    }else{
                        $(t).addClass('btn-info').removeClass('btn-danger');
                        $(t).html(lang.active);
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                   My.ajax_error_message(xhr);
               },
            });

        },
        empty: function () {
            $('#id').val(0);
            $('.image_box').html('<img src="' + config.url + '/no-image.png" class="image" width="150" height="80" />');
            $('.vehicle_image_box').html('<img src="' + config.url + '/no-image.png" class="vehicle_image" width="150" height="80" />');
            $('.license_image_box').html('<img src="' + config.url + '/no-image.png" class="license_image" width="150" height="80" />');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    Delegates.init();
});

