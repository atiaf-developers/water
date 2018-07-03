var AccountTypes_grid;
var AccountTypes = function () {

    var init = function () {

        $.extend(lang, new_lang);
        $.extend(config, new_config);
        handleRecords();
        handleSubmit();
    };

    var handleRecords = function () {

        AccountTypes_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/account_types/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "title", "name": "account_types_translations.title"},
                {"data": "active", "name": "account_types.active", searchable: false},
                {"data": "this_order", "name": "account_types.this_order", searchable: false},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [2, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {

        if ($('#addEditAccountTypesForm').length > 0) {


            $('#addEditAccountTypesForm').validate({
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
            $('#addEditAccountTypesForm .submit-form').click(function () {

                if ($('#addEditAccountTypesForm').validate().form()) {
                    $('#addEditAccountTypesForm .submit-form').prop('disabled', true);
                    $('#addEditAccountTypesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditAccountTypesForm').submit();
                    }, 1000);
                }
                return false;
            });
            $('#addEditAccountTypesForm input').keypress(function (e) {
                if (e.which == 13) {
                    if ($('#addEditAccountTypesForm').validate().form()) {
                        $('#addEditAccountTypesForm .submit-form').prop('disabled', true);
                        $('#addEditAccountTypesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                        setTimeout(function () {
                            $('#addEditAccountTypesForm').submit();
                        }, 1000);
                    }
                    return false;
                }
            });



            $('#addEditAccountTypesForm').submit(function () {
                var id = $('#id').val();
                var action = config.admin_url + '/account_types';
                var formData = new FormData($(this)[0]);
                if (id != 0) {
                    formData.append('_method', 'PATCH');
                    action = config.admin_url + '/account_types/' + id;
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
                        $('#addEditAccountTypesForm .submit-form').prop('disabled', false);
                        $('#addEditAccountTypesForm .submit-form').html(lang.save);

                        if (data.type == 'success')
                        {
                            My.toast(data.message);
                            if (id == 0) {
                                AccountTypes.empty();
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
                        $('#addEditAccountTypesForm .submit-form').prop('disabled', false);
                        $('#addEditAccountTypesForm .submit-form').html(lang.save);
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
                url: config.admin_url + '/account_types/' + id,
                success: function (data)
                {
                    console.log(data);

                    AccountTypes.empty();
                    My.setModalTitle('#addEditAccountTypes', lang.edit);

                    for (i in data.message)
                    {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditAccountTypes').modal('show');
                }
            });

        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/account_types/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    AccountTypes_grid.api().ajax.reload();
                }
            });

        },
        add: function () {
            AccountTypes.empty();
            My.setModalTitle('#addEditAccountTypes', lang.add);
            $('#addEditAccountTypes').modal('show');
        },
        empty: function () {
                $('#id').val(0);
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    AccountTypes.init();
});

