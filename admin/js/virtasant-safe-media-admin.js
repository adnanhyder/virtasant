(function ($) {
    'use strict';

    $(document).on("click", ".delete-attachment", function (e) {
        e.preventDefault();

        let attachment_url = $(this).parent().parent().find("#attachment-details-two-column-copy-link").val();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'vitrasant_delete',
                post_url: attachment_url
            },
            success: function (response) {
                if (response.code == 1) {
                    reset_library()
                    const newURL = location.href.split("?")[0];
                    window.history.pushState('object', document.title, newURL);
                    $(".media-modal").parent().hide();
                    $("body").removeClass("modal-open");
                } else {
                    if(response.msg){
                        alert(response.msg);
                    }

                }
            }
        });
    });

    function reset_library() {
        if(wp.media.frame.library){
            wp.media.frame.library.props.set({ignore: (+ new Date())});
        } else if(wp.media.frame.content.get().collection){
            wp.media.frame.content.get().collection.props.set({ignore: (+ new Date())});
            wp.media.frame.content.get().options.selection.reset();
        }
    }


})(jQuery);



