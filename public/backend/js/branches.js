var Branches_grid;
var Branches = function () {

    var init = function () {

        $.extend(lang, new_lang);
        $.extend(config, new_config);
        handleRecords();
        handleSubmit();
        
        if($('#map').length > 0){
            Map.initMap(true,true,true,false);
        }
    };

    var handleRecords = function () {

        Branches_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/branches/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "title", "name": "branches_translations.title"},
                {"data": "active", "name": "branches.active", searchable: false},
                {"data": "this_order", "name": "branches.this_order", searchable: false},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [2, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {

        if ($('#addEditBranchesForm').length > 0) {


            $('#addEditBranchesForm').validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    mobile: {
                        required: true,
                    },
                    phone: {
                        required: true,
                    },
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
            $('#addEditBranchesForm .submit-form').click(function () {

                if ($('#addEditBranchesForm').validate().form()) {
                    $('#addEditBranchesForm .submit-form').prop('disabled', true);
                    $('#addEditBranchesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditBranchesForm').submit();
                    }, 1000);
                }
                return false;
            });
            $('#addEditBranchesForm input').keypress(function (e) {
                if (e.which == 13) {
                    if ($('#addEditBranchesForm').validate().form()) {
                        $('#addEditBranchesForm .submit-form').prop('disabled', true);
                        $('#addEditBranchesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                        setTimeout(function () {
                            $('#addEditBranchesForm').submit();
                        }, 1000);
                    }
                    return false;
                }
            });



            $('#addEditBranchesForm').submit(function () {
                var id = $('#id').val();
                var action = config.admin_url + '/branches';
                var formData = new FormData($(this)[0]);
                if (id != 0) {
                    formData.append('_method', 'PATCH');
                    action = config.admin_url + '/branches/' + id;
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
                        $('#addEditBranchesForm .submit-form').prop('disabled', false);
                        $('#addEditBranchesForm .submit-form').html(lang.save);

                        if (data.type == 'success')
                        {
                            My.toast(data.message);
                            if (id == 0) {
                                Branches.empty();
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
                        $('#addEditBranchesForm .submit-form').prop('disabled', false);
                        $('#addEditBranchesForm .submit-form').html(lang.save);
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
                url: config.admin_url + '/branches/' + id,
                success: function (data)
                {
                    console.log(data);

                    Branches.empty();
                    My.setModalTitle('#addEditBranches', lang.edit);

                    for (i in data.message)
                    {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditBranches').modal('show');
                }
            });

        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/branches/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    Branches_grid.api().ajax.reload();
                }
            });

        },
        add: function () {
            Branches.empty();
            My.setModalTitle('#addEditBranches', lang.add);
            $('#addEditBranches').modal('show');
        },
        empty: function () {
            $('#id').val(0);
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    Branches.init();
});

