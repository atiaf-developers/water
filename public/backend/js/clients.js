
var Clients_grid;
var Clients = function () {
    var init = function () {
        //alert('heree');
        $.extend(lang, new_lang);
        //console.log(lang);
        handleRecords();

    };


    var handleRecords = function () {

        Clients_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/clients/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "id"},
                {"data": "username"},
                {"data": "mobile"},
                {"data": "email"},
                {"data": "image"},
                {"data": "active"},
                {"data": "created_at"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [5, "desc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }



    return{
        init: function () {
            init();
        },

        delete: function (t) {
            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/clients/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {

                    Clients_grid.api().ajax.reload();


                }
            });
        },

        empty: function () {
            $('#id').val(0);
            $('#active').find('option').eq(0).prop('selected', true);
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        },

        status: function (t) {
            var client_id = $(t).data("id");
            $(t).prop('disabled', true);
            $(t).html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');

            $.ajax({
                url: config.admin_url + '/clients/status/' + client_id,
                success: function (data) {

                    $(t).prop('disabled', false);

                    Clients_grid.api().ajax.reload();
                },
                error: function (xhr, textStatus, errorThrown) {
                    My.ajax_error_message(xhr);
                },
            });

        },
    };
}();
$(document).ready(function () {
    Clients.init();
});