jQuery(document).ready(function($) {
		var titleTag = $( '#poststuff input#title[name=post_title]' );
		var metaboxTitle = $( '#push_monkey_post_opt_out strong#push_monkey_preview_title' );
		var metaboxContent = $( '#push_monkey_post_opt_out span#push_monkey_preview_content' );

		titleTag.keyup(function() {

				var title = $( this ).attr( 'value' );
				var max_len_title = 33;
				if ( title.length > max_len_title ) {

					title = title.substring( 0, max_len_title ) + '...';
				}
				if (!push_monkey_preview_locals.is_custom_text) {

					metaboxTitle.html(title);					
				} else {

					metaboxContent.text(title);
				}
			});
		if (!push_monkey_preview_locals.is_custom_text) {

			setInterval(function() {

					var content;
					if ( tinyMCE.activeEditor ) {

						content = tinyMCE.activeEditor.getContent();
					} else {

						content = $( 'textarea#content' ).val();
					}

					var max_len_content = 70;
					content = content.replace(/(<([^>]+)>)/ig,"");
					if ( content.length > max_len_content ) {

						content = content.substring( 0, max_len_content ) + '...';
					}
					metaboxContent.text( content );
				}, 1000);
		}
	});
