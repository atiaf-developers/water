var Settings = function() {

    var init = function() {
        //$.extend(lang, new_lang);
        handleSubmit();
        My.readImageMulti('about_image');
        Map.initMap(true, true, true, false);
    };

    var handleSubmit = function() {

        $('#editSettingsForm').validate({
            ignore: "",
            rules: {
                'setting[phone]': {
                    required: true
                },
                'setting[email]': {
                    required: true,
                },
                'setting[map_range]': {
                    required: true,
                },
                'setting[commission]': {
                    required: true,
                },
                'setting[tax]': {
                    required: true,
                },

            },
            messages: lang.messages,
            highlight: function(element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('').css('opacity', 0);
            },
            errorPlacement: function(error, element) {
                $(element).closest('.form-group').find('.help-block').html($(error).html()).css('opacity', 1);
            }
        });
        var langs = JSON.parse(config.languages);
        for (var x = 0; x < langs.length; x++) {
            var about = "textarea[name='setting[about][" + langs[x] + "]']";
            var rating_message_active = "textarea[name='setting[rating_message_active][" + langs[x] + "]']";
            var rating_message_not_active = "textarea[name='setting[rating_message_not_active][" + langs[x] + "]']";
            $(about).rules('add', {
                required: true
            });
            $(rating_message_active).rules('add', {
                required: true
            });
            $(rating_message_not_active).rules('add', {
                required: true
            });
            console.log(rating_message_active);
        }
        $('#editSettingsForm .submit-form').click(function() {
            if ($('#editSettingsForm').validate().form()) {
                $('#editSettingsForm .submit-form').prop('disabled', true);
                $('#editSettingsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function() {
                    $('#editSettingsForm').submit();
                }, 500);
            }
            return false;
        });
        $('#editSettingsForm input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#editSettingsForm').validate().form()) {
                    $('#editSettingsForm .submit-form').prop('disabled', true);
                    $('#editSettingsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function() {
                        $('#editSettingsForm').submit();
                    }, 500);
                }
                return false;
            }
        });



        $('#editSettingsForm').submit(function() {
            var id = $('#id').val();
            var action = config.admin_url + '/settings';
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#editSettingsForm .submit-form').prop('disabled', false);
                    $('#editSettingsForm .submit-form').html(lang.save);

                    if (data.type == 'success') {

                        toastr.options = {
                            "debug": false,
                            "positionClass": "toast-bottom-left",
                            "onclick": null,
                            "fadeIn": 300,
                            "fadeOut": 1000,
                            "timeOut": 5000,
                            "extendedTimeOut": 1000
                        };
                        toastr.success(lang.updated_successfully, 'رسالة');

                    } else {
                        console.log(data)
                       if (typeof data.errors === 'object') {
                            for (i in data.errors) {
                                var message = data.errors[i];
                                var key_arr = i.split('.');
                                var name = '';
                                for (var x = 0; x < key_arr.length; x++) {
                                    if (x == 0) {
                                        name += key_arr[x];
                                    } else {
                                        name += '[' + key_arr[x] + ']';
                                    }
                                }
                                i = name;


                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(message).css('opacity', 1);
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
                error: function(xhr, textStatus, errorThrown) {
                    $('#editSettingsForm .submit-form').prop('disabled', false);
                    $('#editSettingsForm .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });

            return false;

        })




    }

    return {
        init: function() {
            init();
        }
    };

}();
jQuery(document).ready(function() {
    Settings.init();
});