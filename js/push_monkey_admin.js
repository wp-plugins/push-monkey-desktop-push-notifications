jQuery(document).ready(function($) {

	$(".push-monkey input[type='checkbox']").bootstrapSwitch();

	var savingLabel = $('#position-save-cue');
	savingLabel.hide();

	$('.push-monkey .select-picker').selectpicker().on('change', function() {

		var val = $(this).val();
		var data = {
			'action': 'push_monkey_banner_position',
			'value': val
		};
		$.post(ajaxurl, data, function(response) {

			savingLabel.fadeIn('slow').delay(3000).fadeOut();
		});

		var demo = $('div.banner-demo');
		var positions = {
			'top': 'banner-top', 
			'bottom': 'banner-bottom',
			'disabled': 'banner-disabled',
			'topLeft': 'banner-top-left',
			'topRight': 'banner-top-right',
			'bottomLeft': 'banner-bottom-left',
			'bottomRight': 'banner-bottom-right',
			'centerLeft': 'banner-center-left',
			'centerRight': 'banner-center-right',
		};
		var className = positions[val];
		demo.find('div').fadeOut();
		demo.append('<div class="banner ' + className + '"></div>');
	});

	var standard_post_enabled = $('#post-types input[value="post"]').bootstrapSwitch('state');
	if (!standard_post_enabled) {

		$('#post-categories input[type=checkbox]').bootstrapSwitch('toggleDisabled');
	};

	$("#post-types input[value='post']").on('switchChange.bootstrapSwitch', function() {

		$('#post-categories input[type=checkbox]').bootstrapSwitch('toggleDisabled');
			
	});
});