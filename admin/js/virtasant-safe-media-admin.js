(function ($) {
    'use strict';
    $(document).on("click", ".delete-attachment-custom", function(e){
        if( ! confirm( error_message.confirm_error  ) ) {
            return false;
        }
        e.preventDefault();
        let attachment_id = $(this).attr("data-attachment-id");
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'vitrasant_delete',
                post_id: attachment_id
            },
            success: function(response) {
                alert(response);
            }
        });
    });
})(jQuery);


