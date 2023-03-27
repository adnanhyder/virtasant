/**
 *
 * All of the code for your Admin-facing JavaScript source
 * should reside in this file.
 *
 * @link       https://profiles.wordpress.org/adnanhyder/
 * @since      1.0.0
 *
 * @package    Virtasant_Safe_Media
 * @subpackage Virtasant_Safe_Media/admin
 * Note: It has been assumed you will write jQuery code here, so the
 * $ function reference has been prepared for usage within the scope
 * of this function.
 *
 * This enables you to define handlers, for when the DOM is ready:
 *
 * $(function() {
 *
 * });
 *
 * When the window is loaded:
 *
 * $( window ).load(function() {
 *
 * });
 *
 * ...and/or other possibilities.
 *
 * Ideally, it is not considered best practise to attach more than a
 * single DOM-ready or window-load handler for a particular page.
 * Although scripts in the WordPress core, Plugins and Themes may be
 * practising this, we should strive to set a better example in our own work.
 */

(function ($) {
	'use strict';
	$( document ).on(
		"click",
		".delete-attachment",
		function (e) {
			e.preventDefault();

			let post_id = $( this ).parent().parent().parent().parent().find( ".compat-field-id th label" ).attr( "for" ).replace( /[^0-9]/g, '' );

			$.ajax(
				{
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'vitrasant_delete',
						post_id: post_id
					},
					success: function (response) {
						if (response.code == 1) {
							reset_library()
							const newURL = location.href.split( "?" )[0];
							window.history.pushState( 'object', document.title, newURL );
							$( ".media-modal" ).parent().hide();
							$( "body" ).removeClass( "modal-open" );
						} else {
							if (response.msg) {
								alert( response.msg );
							}

						}
					}
				}
			);
		}
	);

	function reset_library() {
		if (wp.media.frame.library) {
			wp.media.frame.library.props.set( {ignore: (+ new Date())} );
		} else if (wp.media.frame.content.get().collection) {
			wp.media.frame.content.get().collection.props.set( {ignore: (+ new Date())} );
			wp.media.frame.content.get().options.selection.reset();
		}
	}

})( jQuery );
