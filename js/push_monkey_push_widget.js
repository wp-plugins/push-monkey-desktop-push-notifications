jQuery(document).ready(function($) {

		$('#push-monkey-push-dashboard-widget .regular-text').unbind('keyup change input paste').bind('keyup change input paste',function(e){

				var $this = $(this);
				var val = $this.val();
				var valLength = val.length;
				var maxCount = $this.attr('maxlength');
				if(valLength>maxCount){
					$this.val($this.val().substring(0, maxCount));
				}
			}); 

		var modalTrigger = $('a[rel*=leanModal]');
		modalTrigger.leanModal({ top : 200, closeButton: ".modal_close" });		

		$('a.close_modal').click(function(){

				var modal_id = $(this).parents('.push_monkey_modal').attr('id');
				close_modal('#'+modal_id);
			});

		$('a.push_monkey_submit').click(function(){

				var modal_id = $(this).parents('.push_monkey_modal').attr('id');
				close_modal('#'+modal_id);
				$('#push-monkey-push-dashboard-widget form').submit();
			});

		function close_modal(modal_id){

			$("#lean_overlay").fadeOut(200);
			$(modal_id).css({ 'display' : 'none' });
		}
	});
