var Products_grid;
var Products = function () {

    var init = function () {

        $.extend(lang, new_lang);
        $.extend(config, new_config);
        handleRecords();
        handleSubmit();
        My.readImageMulti('image');

    };

    var handleRecords = function () {

        Products_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/products/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "title", "name": "products_translations.title"},
                {"data": "active", "name": "products.active", searchable: false},
                {"data": "this_order", "name": "products.this_order", searchable: false},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [2, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {

        if ($('#addEditProductsForm').length > 0) {


            $('#addEditProductsForm').validate({
                ignore: "",
                rules: {
                    quantity: {
                        required: true,
                    },
                    price: {
                        required: true,
                    },
                    category: {
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
                var description = "input[name='description[" + langs[x] + "]']";
                var image = "input[name='image']";
                $(title).rules('add', {
                    required: true
                });
                $(description).rules('add', {
                    required: true
                });
            }
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
            $('#addEditProductsForm .submit-form').click(function () {

                if ($('#addEditProductsForm').validate().form()) {
                    $('#addEditProductsForm .submit-form').prop('disabled', true);
                    $('#addEditProductsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditProductsForm').submit();
                    }, 1000);
                }
                return false;
            });
            $('#addEditProductsForm input').keypress(function (e) {
                if (e.which == 13) {
                    if ($('#addEditProductsForm').validate().form()) {
                        $('#addEditProductsForm .submit-form').prop('disabled', true);
                        $('#addEditProductsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                        setTimeout(function () {
                            $('#addEditProductsForm').submit();
                        }, 1000);
                    }
                    return false;
                }
            });



            $('#addEditProductsForm').submit(function () {
                var id = $('#id').val();
                var action = config.admin_url + '/products';
                var formData = new FormData($(this)[0]);
                if (id != 0) {
                    formData.append('_method', 'PATCH');
                    action = config.admin_url + '/products/' + id;
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
                        $('#addEditProductsForm .submit-form').prop('disabled', false);
                        $('#addEditProductsForm .submit-form').html(lang.save);

                        if (data.type == 'success')
                        {
                            My.toast(data.message);
                            if (id == 0) {
                                Products.empty();
                            }


                        } else {
                            if (typeof data.errors !== 'undefined') {
                                for (i in data.errors)
                                {
                                    var message = data.errors[i];
                                    if (i.startsWith('title') || i.startsWith('description') || i.startsWith('gallery')) {
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
                        $('#addEditProductsForm .submit-form').prop('disabled', false);
                        $('#addEditProductsForm .submit-form').html(lang.save);
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
                url: config.admin_url + '/products/' + id,
                success: function (data)
                {
                    console.log(data);

                    Products.empty();
                    My.setModalTitle('#addEditProducts', lang.edit);

                    for (i in data.message)
                    {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditProducts').modal('show');
                }
            });

        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/products/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    Products_grid.api().ajax.reload();
                }
            });

        },
        add: function () {
            Products.empty();
            My.setModalTitle('#addEditProducts', lang.add);
            $('#addEditProducts').modal('show');
        },
        empty: function () {
            $('#id').val(0);
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    Products.init();
});

