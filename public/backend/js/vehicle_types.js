var VehicleTypes_grid;
var VehicleTypes = function () {

    var init = function () {

        $.extend(lang, new_lang);
        $.extend(config, new_config);
        handleRecords();
        handleSubmit();
        My.readImageMulti('image');
    };

    var handleRecords = function () {

        VehicleTypes_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/vehicle_types/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "title", "name": "vehicle_types_translations.title"},
                {"data": "image", orderable: false, searchable: false},
                {"data": "active", "name": "vehicle_types.active", searchable: false},
                {"data": "this_order", "name": "vehicle_types.this_order", searchable: false},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [3, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {

        if ($('#addEditVehicleTypesForm').length > 0) {


            $('#addEditVehicleTypesForm').validate({
                ignore: "",
                rules: {
                    active: {
                        required: true,
                    },
                    this_order: {
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
            var langs = JSON.parse(config.languages);
            for (var x = 0; x < langs.length; x++) {
                var title = "input[name='title[" + langs[x] + "]']";
                $(title).rules('add', {
                    required: true
                });
            }
            var image = "input[name='image']";
            if (config.action == 'add') {
                $(image).rules('add', {
                    required: true,
                    accept: "image/*",
                    filesize: 1000 * 1024
                });
            } else {
                $(image).rules('add', {
                    accept: "image/*",
                    filesize: 1000 * 1024
                });
            }
            $('#addEditVehicleTypesForm .submit-form').click(function () {

                if ($('#addEditVehicleTypesForm').validate().form()) {
                    $('#addEditVehicleTypesForm .submit-form').prop('disabled', true);
                    $('#addEditVehicleTypesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditVehicleTypesForm').submit();
                    }, 1000);
                }
                return false;
            });
            $('#addEditVehicleTypesForm input').keypress(function (e) {
                if (e.which == 13) {
                    if ($('#addEditVehicleTypesForm').validate().form()) {
                        $('#addEditVehicleTypesForm .submit-form').prop('disabled', true);
                        $('#addEditVehicleTypesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                        setTimeout(function () {
                            $('#addEditVehicleTypesForm').submit();
                        }, 1000);
                    }
                    return false;
                }
            });



            $('#addEditVehicleTypesForm').submit(function () {
                var id = $('#id').val();
                var action = config.admin_url + '/vehicle_types';
                var formData = new FormData($(this)[0]);
                if (id != 0) {
                    formData.append('_method', 'PATCH');
                    action = config.admin_url + '/vehicle_types/' + id;
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
                        $('#addEditVehicleTypesForm .submit-form').prop('disabled', false);
                        $('#addEditVehicleTypesForm .submit-form').html(lang.save);

                        if (data.type == 'success')
                        {
                            My.toast(data.message);
                            if (id == 0) {
                                VehicleTypes.empty();
                            }


                        } else {
                            if (typeof data.errors !== 'undefined') {
                                for (i in data.errors)
                                {
                                    var message = data.errors[i];
                                    if (i.startsWith('title')) {
                                        var key_arr = i.split('.');
                                        var key_text = key_arr[0] + '[' + key_arr[1] + ']';
                                        i = key_text;
                                    }

                                    $('[name="' + i + '"]')
                                            .closest('.form-group').addClass('has-error');
                                    $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(message).css('opacity', 1);
                                }
                            }
                        }
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        $('#addEditVehicleTypesForm .submit-form').prop('disabled', false);
                        $('#addEditVehicleTypesForm .submit-form').html(lang.save);
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
        edit: function (t) {
            var id = $(t).attr("data-id");
            My.editForm({
                element: t,
                url: config.admin_url + '/vehicle_types/' + id,
                success: function (data)
                {
                    console.log(data);

                    VehicleTypes.empty();
                    My.setModalTitle('#addEditVehicleTypes', lang.edit);

                    for (i in data.message)
                    {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditVehicleTypes').modal('show');
                }
            });

        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/vehicle_types/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    VehicleTypes_grid.api().ajax.reload();
                }
            });

        },
        add: function () {
            VehicleTypes.empty();
            My.setModalTitle('#addEditVehicleTypes', lang.add);
            $('#addEditVehicleTypes').modal('show');
        },
        empty: function () {
            $('#id').val(0);
            $('.image').html('<img style="height:80px;width:150px;" class="image"  src="' + config.public_path + '/uploads/vehicle_types/' + data.message[i] + '" alt="your image" />');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    VehicleTypes.init();
});

