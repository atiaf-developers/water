var RejectionReasons_grid;
var RejectionReasons = function () {

    var init = function () {

        $.extend(lang, new_lang);
        $.extend(config, new_config);
        handleRecords();
        handleSubmit();
    };

    var handleRecords = function () {

        RejectionReasons_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/rejection_reasons/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "title", "name": "rejection_reasons_translations.title"},
                {"data": "active", "name": "rejection_reasons.active", searchable: false},
                {"data": "this_order", "name": "rejection_reasons.this_order", searchable: false},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [2, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {

        if ($('#addEditRejectionReasonsForm').length > 0) {


            $('#addEditRejectionReasonsForm').validate({
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
            $('#addEditRejectionReasonsForm .submit-form').click(function () {

                if ($('#addEditRejectionReasonsForm').validate().form()) {
                    $('#addEditRejectionReasonsForm .submit-form').prop('disabled', true);
                    $('#addEditRejectionReasonsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditRejectionReasonsForm').submit();
                    }, 1000);
                }
                return false;
            });
            $('#addEditRejectionReasonsForm input').keypress(function (e) {
                if (e.which == 13) {
                    if ($('#addEditRejectionReasonsForm').validate().form()) {
                        $('#addEditRejectionReasonsForm .submit-form').prop('disabled', true);
                        $('#addEditRejectionReasonsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                        setTimeout(function () {
                            $('#addEditRejectionReasonsForm').submit();
                        }, 1000);
                    }
                    return false;
                }
            });



            $('#addEditRejectionReasonsForm').submit(function () {
                var id = $('#id').val();
                var action = config.admin_url + '/rejection_reasons';
                var formData = new FormData($(this)[0]);
                if (id != 0) {
                    formData.append('_method', 'PATCH');
                    action = config.admin_url + '/rejection_reasons/' + id;
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
                        $('#addEditRejectionReasonsForm .submit-form').prop('disabled', false);
                        $('#addEditRejectionReasonsForm .submit-form').html(lang.save);

                        if (data.type == 'success')
                        {
                            My.toast(data.message);
                            if (id == 0) {
                                RejectionReasons.empty();
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
                        $('#addEditRejectionReasonsForm .submit-form').prop('disabled', false);
                        $('#addEditRejectionReasonsForm .submit-form').html(lang.save);
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
                url: config.admin_url + '/rejection_reasons/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    RejectionReasons_grid.api().ajax.reload();
                }
            });

        },
        add: function () {
            RejectionReasons.empty();
            My.setModalTitle('#addEditRejectionReasons', lang.add);
            $('#addEditRejectionReasons').modal('show');
        },
        empty: function () {
                $('#id').val(0);
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    RejectionReasons.init();
});

