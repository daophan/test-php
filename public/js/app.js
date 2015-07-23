$(document).ready(function() {
    var nImage = 0;
    var uploadRow;

    function init(){

        $('.popup .bt_close').click(function(event) {
            popup.hide();
        });

        $('#bt_add_image').click(function(event) {
            popup.show('upload');
        });

        $('.bt_edit_image').click(function(event) {
            var box = $(this).parent().parent();
            popup.show('edit');
            console.log(box);
            popup.edit.load(box);
        });

        $('#bt_add_more').click(function(event) {
            popup.upload.add_more();
        });

        $('.bt_remove_row').click(function(event) {
            var row = $(this).parent().parent();
            popup.upload.remove_row(row);
            popup.upload.update_row_index();
        });

        $('.bt_add_image').click(function(event) {
            $(this).prev().click();
        });

        $('.bt_remove_image').click(function(event) {
            var box = $(this).parent().parent();
            $.ajax({
                url: 'image/delete',
                type: 'POST',
                dataType: 'json',
                data: { pid: box.attr('pid') }
            })
            .done(function(data) {
                if(data.status == true)
                    box.remove();
            });

        });

        $('#upload_images_form').submit(function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr("action"),
                type: 'POST',
                data: formData,
                async: true,
                success: function (data) {
                    if(data.status)
                        location.reload();
                    else
                        alert(data.message);
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

        $('#edit_image_form').submit(function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr("action"),
                type: 'POST',
                data: formData,
                async: true,
                success: function (data) {
                    if(data.status)
                        location.reload();
                    else
                        alert(data.message);
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

        $(".upload_row input:file").change(function (){
            var row = $(this).parent().parent();
            var fileName = $(this).val();
            popup.upload.preview_image(row.find('.preview_image').first(), this);
        });

        $(".edit_panel input:file").change(function (){
            var row = $(this).parent().parent();
            var fileName = $(this).val();
            popup.upload.preview_image(row.find('.preview_image').first(), this);
        });

        uploadRow = $('.upload_panel .upload_row').first().clone(true, true);
    }

    var popup = {
        show : function(popupName){
            $('.popup').hide();
            $('.popup_' + popupName).show();
        },
        hide : function(){
            $('.popup').hide();
        },
        upload : {
            add_more : function()
            {
                var new_upload_row = uploadRow.clone(true, true)
                new_upload_row.find('.num').html($('.upload_panel').children().length + 1);
                $('.upload_panel').append(new_upload_row);
            },
            remove_row : function(el)
            {
                el.remove();
            },
            update_row_index : function()
            {
                $('.upload_panel').children().each(function(index, el) {
                    $(el).find('.num').html(index + 1);
                });
            },
            preview_image : function(div, input)
            {
                var image = $('<img width="225" height="100" src="">');
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        image.attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
                div.html(image);
            }
        },
        edit : {
            load : function(box){
                $('#image_edit_id').val(box.attr('pid'));
                $('.edit_panel .image_edit_name').val(box.find('.file_name').first().html());
                $('.edit_panel .preview_image img').attr('src', box.find('img').first().attr('src'));
            }
        }
    };

    init();
});