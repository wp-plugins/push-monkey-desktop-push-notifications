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

	$('div.push-monkey-us-notice .close-btn a').click(function() {

		$('div.push-monkey-us-notice').fadeOut();
		CookieManager.setCookie('push_monkey_us_notice', true, 8);
	});

	$('div.push-monkey-welcome-notice .close-btn a').click(function(){

		$('div.push-monkey-welcome-notice').parents('div.push-monkey').fadeOut();
		CookieManager.setCookie('push_monkey_welcome_notice', true, 60);
	}); 

	$("div.banner-color-input").colorpicker({
		container: $('#picker_container'),
	});

	$('input[type=radio]').visualRadioInput({
		selected: function(radioInput) {

			if (radioInput.val() == 'static-title') {

				$('input#custom-text').prop('disabled', false);
			} else {

				$('input#custom-text').prop('disabled', true);				
			}
		}
	});

	var initialText = $('#push_monkey_preview_title').text();
	$('input#custom-text').on("change keyup paste", function(){

		var el = $(this);
		var value = el.val();
		if (!value.length) {

			value = initialText;
		}
		$('#push_monkey_preview_title').html(value);
	});
});

var CookieManager = {};
CookieManager.setCookie = function(name, value, days) {

    var expires;
    if (days) {

        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {

        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

CookieManager.getCookie = function(c_name) {

    if (document.cookie.length > 0) {

        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {

            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {

                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return false;
}

(function ($) {

	$.fn.visualRadioInput = function(options) {

		var defaultOptions = $.extend({

			selected: function(radioInput) {
			}
		}, options );
		var foundElements = this;
		return this.each(function(i, e) {

			var el = $(e);
			el.hide();
			var visualInput = el.siblings('.selection-box');
			if (visualInput.hasClass('selected')) {

				visualInput.find('div.checkmark').show();
				el.prop('checked', true);
			} else {

				visualInput.find('div.checkmark').hide();
				el.prop('checked', false);
			}
			visualInput.click(function(){

				var clickedEl = $(this);
				if (! clickedEl.hasClass('selected')) {

					$('form div.selection-box.selected').each(function(index, e) {

						var el = $(e);
						el.removeClass('selected');
						el.find('.checkmark').hide();
					});
					clickedEl.addClass('selected');
					clickedEl.find('.checkmark').show();
					foundElements.each(function(j, e){

						if (i == j) {

							$(e).prop('checked', true);
						} else {

							$(e).prop('checked', false);
						}
					});
					options.selected(el);
				}
			});
		});
	};
}(jQuery));