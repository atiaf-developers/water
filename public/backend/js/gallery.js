


var Gallery = function () {

    var init = function () {
        $.extend(lang, new_lang);
        $.extend(config, new_config);
        //alert('here');
        handleUpload();
        handleChangeArrowLeftAndRight();
        handleDeleteUploadPic();

    };
    var handleDeleteUploadPic = function () {
        $(document).off('click', '.deleteClassifiedUploadedPic');
        $(document).on('click', '.deleteClassifiedUploadedPic', function () {
            var $this = $(this);
            $(this).parent().parent().remove();
            if ($('.classifiedUploadedPicContainer').length == 0) {
                $('.uploadedPics').html('<p class="text-center  empty-message">' + lang.no_images + '</p>')
            }

        });
    }
    var handleChangeArrowLeftAndRight = function () {

        $(document).off('click', '.moveClassifiedUploadedPicRight');
        $(document).on('click', '.moveClassifiedUploadedPicRight', function () {
            var thisPic = $(this).parent().parent();
            var toReplace = thisPic.prev('.classifiedUploadedPicContainer');
            if (toReplace.length > 0) {
                toReplace.insertAfter(thisPic);
            }
        });


        $(document).off('click', '.moveClassifiedUploadedPicLeft');
        $(document).on('click', '.moveClassifiedUploadedPicLeft', function () {
            var thisPic = $(this).parent().parent();
            var toReplace = thisPic.next('.classifiedUploadedPicContainer');
            if (toReplace.length > 0) {
                toReplace.insertBefore(thisPic);
            }
        });
    }

    var handleUpload = function () {

        $(document).off('click', '.submit-form');
        $(document).on('click', '.submit-form', function () {
            $('.submit-form').prop('disabled', true);
            $('.submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
            setTimeout(function () {
                uploadFile($("#gallery"), function (response) {
                    console.log(response);

                    if (response.type === 'error') {
                        $('#progress_div').hide();
                        $.alert({
                            title: lang.error,
                            content: response.message,
                            type: 'red',
                            typeAnimated: true,
                            buttons: {
                                tryAgain: {
                                    text: lang.try_again,
                                    btnClass: 'btn-red',
                                    action: function () {
                                    }
                                }
                            }
                        });
                        return false;
                    } else if (response.type === 'success') {
                        $('.submit-form').prop('disabled', false);
                        $('.submit-form').html(lang.save);
                        Gallery.empty();

                        if (typeof response.data.uploaded !== "undefined")
                        {
                            $('.empty-message').remove();
                            $('#progress_div').hide();
                            var items = [];
                            var count = 1;
                            for (var x = 0; x < response.data.uploaded.length; x++) {
                                var image = response.data.uploaded[x];
                                var html = '<div class="classifiedUploadedPicContainer"  style="margin-bottom: 10px;">' +
                                        '<input type="hidden" name="uploaded[]" value="' + image + '">' +
                                        '<div class="classifiedUploadedPic" style="background-image:url(' + config.url + '/public/uploads/products/' + image + ');"></div>';
                                if (config.lang_code == 'ar') {
                                    html += '<div class="classifiedUploadedPicTools">' +
                                            '<button class="moveClassifiedUploadedPicRight" type="button"><i class="fa fa-arrow-right"></i></button>' +
                                            '<button class="deleteClassifiedUploadedPic" type="button"><i class="fa fa-trash-o"></i></button>' +
                                            '<button class="moveClassifiedUploadedPicLeft" type="button"><i class="fa fa-arrow-left"></i></button>' +
                                            '</div>';
                                } else {
                                    html += '<div class="classifiedUploadedPicTools">' +
                                            '<button class="moveClassifiedUploadedPicRight" type="button"><i class="fa fa-arrow-left"></i></button>' +
                                            '<button class="deleteClassifiedUploadedPic" type="button"><i class="fa fa-trash-o"></i></button>' +
                                            '<button class="moveClassifiedUploadedPicLeft" type="button"><i class="fa fa-arrow-right"></i></button>' +
                                            '</div>';
                                }

                                html += '</div>';

                                items.push(html);
                                count++;
                            }
                            if ($('.img-box').length == 0) {
                                $(".delete-box").html('<div class="clearfix"></div><button type="button" disabled class="btn btn-info delete-gallery-images">' + lang.delete + '</button>');

                            }
                            $(".uploadedPics").append(items.join(''));
                        }
                        if (typeof response.data.message !== "undefined")
                        {
                            My.toast(response.data.message);
                        }



                    }

                }, function ()
                {
                    var xhr = new window.XMLHttpRequest();
                    currentUploadProgress = 0;
                    var inputFile = $('#gallery');
                    var fileToUpload = inputFile[0].files;
                    if (fileToUpload.length > 0) {
                        xhr.upload.addEventListener("progress", function (evt) {
                            $('#progress_div').show();
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;


                                var i = Math.round(percentComplete * 100);
                                var i2 = Math.round(percentComplete * 100);
                                if (i > currentUploadProgress) {
                                    currentUploadProgress = i;
                                    $('#progress_div').find('.progress-bar').css({'width': i + '%'});
                                    $('#progress_div').find('#percent').html(i + '%');
                                }
                            }
                        }, false);
                    }





                    return xhr;
                });
            }, 1000);




        });



    }
    var uploadFile = function (element, callback, xhrFunction, additionalItems) {
        var formData = new FormData($('#galleryForm')[0]);
//        var inputFile = $('#gallery');
//        var fileToUpload = inputFile[0].files;
//        for (var x = 0; x < fileToUpload.length; x++) {
//            formData.append('gallery[]', fileToUpload[x]);
//        }
        if (additionalItems && Object.keys(additionalItems).length > 0) {
            $.each(additionalItems, function (k, v) {
                formData.append(k, v);
            });
        }

        uploadXhr = $.ajax({
            type: "POST",
            dataType: "json",
            url: config.upload_url,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            xhr: xhrFunction

        }).done(function (data) {
            callback(data);

        }).fail(function (xhr) {
            $('.submit-form').prop('disabled', false);
            $('.submit-form').html(lang.save);
            My.ajax_error_message(xhr);

        });



    }


    return {
        init: function () {
            init();
        },
        empty: function () {
            $('#gallery').val("");
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    Gallery.init();
});

