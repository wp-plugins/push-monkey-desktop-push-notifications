jQuery( document ).ready(function( $ ) {
		var titleTag = $( '#poststuff input#title[name=post_title]' );
		var metaboxTitle = $( '#push_monkey_post_opt_out strong#push_monkey_preview_title' );
		var metaboxContent = $( '#push_monkey_post_opt_out span#push_monkey_preview_content' );
		titleTag.keyup(function() {

				var title = $( this ).attr( 'value' );
				var max_len_title = 33;
				if ( title.length > max_len_title ) {

					title = title.substring( 0, max_len_title ) + '...';
				}
				metaboxTitle.html( title );
			});
		setInterval(function() {

				var content = tinyMCE.activeEditor.getContent();
				var max_len_content = 70;
				content = content.replace(/(<([^>]+)>)/ig,"");
				if ( content.length > max_len_content ) {

					content = content.substring( 0, max_len_content ) + '...';
				}
				metaboxContent.text( content );
			}, 1000);
	});
