/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document, console */

$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        url: $('#fileupload').attr('action'),
        autoUpload: attConfig.autoUpload,
        sequentialUploads: attConfig.sequential
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            'plugins/attach2/lib/upload/cors/result.html?%s'
        )
    );

    // Load existing files:
    // $('#fileupload').each(function () {
    //     var that = this;
    //     $.getJSON(this.action, function (result) {
    //         if (result && result.length) {
    //             $(that).fileupload('option', 'done')
    //                 .call(that, null, {result: result});
    //         }
    //     });
    // });
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#fileupload').fileupload('option', 'url'),
        dataType: 'json',
        context: $('#fileupload')[0]
    }).done(function (result) {
        $(this).fileupload('option', 'done')
            .call(this, null, {result: result});
    });

    if (window.FormData) {
        // Replacement of existing images
        // Supported on moder browsers only
        $('.container').on('change', 'input.att-replace-file', function() {
            var id   = $(this).attr('data-id');
            var filename = $(this).val();
            var pass = true;
            if (attConfig.exts.length > 0) {
                // Examine file extension
                var m = /\.(\w+)$/.exec(filename);
                if (m) {
                    var ext = m[1];
                    pass = attConfig.exts.indexOf(ext.toLowerCase()) != -1;
                } else {
                    pass = false;
                }
            }
            if (pass) {
                $('button.att-replace-button[data-id="'+id+'"]').show();
            } else {
                $('button.att-replace-button[data-id="'+id+'"]').hide();
            }
        });

        $('.container').on('click', 'button.att-replace-button', function() {
            var id   = $(this).attr('data-id');
            var input = document.getElementById("att-file"+id);
            var formdata = new FormData();
            if (input.files.length != 1) {
                return false;
            }
            var file = input.files[0];
            // TODO check file.type against attConfig.accept
            formdata.append('file', file);

            var x = $('input[name="x"][type="hidden"]').first().val();
            var updateUrl = 'index.php?r=attach2&a=replace&id='+id+'&x='+x;

            $('.fileupload-loading').show();

            $.ajax({
                url: updateUrl,
                type: "POST",
                data: formdata,
                processData: false,
                contentType: false,
                success: function (res) {
                    $('.fileupload-loading').hide();
                    // Reload the frame
                    window.location.reload();
                }
            });
            return false;
        });
    }

    // Title editing for uploaded items
    $('.container').on('change', 'input.att-edit-title', function() {
        var that = this;
        var id   = $(this).attr('data-id');
        var x    = $('input[name="x"][type="hidden"]').first().val();

        var updateUrl = 'index.php?r=attach2&a=update_title&id='+id+'&x='+x;

        var value = $(this).val();

        $('.fileupload-loading').show();
        $(this).attr('disabled', true);

        $.post(updateUrl, {title: value}, function() {
            $(that).attr('disabled', false);
            $('.fileupload-loading').hide();
        });

    });

    // Drag&Drop for reordering
    setTimeout(function() {
        $("#attTable").tableDnD({
            onDrop: function(table, row) {
                var orders = [];
                var i = 0;
                $("#attTable td.name").each(function() {
                    var id = $(this).find('input[name="title"]').attr('data-id');
                    orders[i] = id;
                    i++;
                });

                var x = $('input[name="x"][type="hidden"]').first().val();
                var updateUrl = 'index.php?r=attach2&a=reorder&area='+attConfig.area+'&item='+attConfig.item+'&x='+x;

                $('.fileupload-loading').show();

                $.post(updateUrl, {orders: orders}, function(data) {
                    $('.fileupload-loading').hide();
                });
            }
        });
    }, 300);

});
