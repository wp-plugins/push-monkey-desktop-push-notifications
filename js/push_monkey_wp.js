/*
 * Version: 0.9.9.9.4
 */

var PushMonkeyWPConfig = {};
PushMonkeyWPConfig.endPoint = push_monkey_locals.endpoint_url + '/push'; // DO NOT CHANGE!
PushMonkeyWPConfig.websiteID = push_monkey_locals.website_push_id; 
PushMonkeyWPConfig.name = push_monkey_locals.website_name;

var PushMonkeyWPLog = {};
PushMonkeyWPLog.store = function (msg) {
	//console.log(msg);
}

var PushMonkeyWPAlert = {};
PushMonkeyWPAlert.confirmation = function () {
	alert('You have succesfully subscribed to Safari Push Notifications from ' + PushMonkeyWPConfig.name);
}

var PushMonkeyWP = {};
PushMonkeyWP.register = function () {
	var checkRemotePermission = function (permissionData) {
		if (permissionData.permission === 'default') {
			PushMonkeyWPLog.store('This is a new web service URL and its validity is unknown.');
			window.safari.pushNotification.requestPermission(
				PushMonkeyWPConfig.endPoint, 
				PushMonkeyWPConfig.websiteID, 
				{}, 
				checkRemotePermission 
			);
		} else if (permissionData.permission === 'denied') {
			PushMonkeyWPLog.store('The user said no.');
		} else if (permissionData.permission === 'granted') {
			PushMonkeyWPLog.store('The web service URL is a valid push provider, and the user said yes.');
			PushMonkeyWPAlert.confirmation();
		}
	};

	//
	// Must fix this bit. No real reason for this here.
	//
	if ('safari' in window && 'pushNotification' in window.safari) {
		var permissionData = window.safari.pushNotification.permission(PushMonkeyWPConfig.websiteID); 
		checkRemotePermission(permissionData);
	} else {
		PushMonkeyWPLog.store('Push Notifications are available for Safari browser only');
	}
}

PushMonkeyWP.check = function(){

	if ( window.safari ) {

		permission = window.safari.pushNotification.permission(PushMonkeyWPConfig.websiteID).permission;
		if( permission == 'default' ) {

			this.register();
		} else {

			PushMonkeyWPLog.store( 'Already registered or rejected.' );
		}
	} else {

		PushMonkeyWPLog.store( 'Not in Safari.' );
	}
}

PushMonkeyWP.setCookie = function(name, value, days) {

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

PushMonkeyWP.getCookie = function(c_name) {

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

PushMonkeyWP.check();

jQuery(document).ready(function($) {

	function generate_banner(type) {

		if (push_monkey_locals.banner_position == 'disabled') {

			return;
		};

		if ( window.location.href == push_monkey_locals.home_url && push_monkey_locals.disabled_on_home ) {

			return;
		}

		var pgwBrowser = $.pgwBrowser();
		if (pgwBrowser.os.group != 'Mac OS') {

			return;
		}

		if (pgwBrowser.browser.group == 'Safari') {

			return;
		};

		var text = '';
		if (push_monkey_locals.banner_position == 'top' || push_monkey_locals.banner_position == 'bottom') {

			text = "<img src='" + push_monkey_locals.banner_icon_url + "' />" + push_monkey_locals.banner_text;
		} else {

			text = "<img src='" + push_monkey_locals.banner_icon_url_v2 + "' />" + push_monkey_locals.banner_text;
		}

		var openAnimation = 'animated fadeInDown';
		var closeAnimation = 'animated fadeOutUp';
		if (push_monkey_locals.banner_position.indexOf('bottom') >= 0) {

			openAnimation = 'animated fadeInUp';
			closeAnimation = 'animated fadeOutDown';
		}

		var n = noty({

			text        : text,
			type        : type,
			dismissQueue: true,
			closeWith   : ['click'],
			layout      : push_monkey_locals.banner_position,
			theme       : 'push-monkey-theme',
			maxVisible  : 10,
			closeWith   : ['button', 'click'],
			animation   : {

                open  : openAnimation,
                close : closeAnimation,
                easing: 'swing',
                speed : 1000
            },
            callback 	: {

            	onShow: function() {

            		$('.noty_container_type_success').css({'background-color':push_monkey_locals.banner_color});
            	},
            	onClose:   function() {

            		var counter_cookie = PushMonkeyWP.getCookie('push_monkey_banner_counter');
            		if (!counter_cookie) {

            			PushMonkeyWP.setCookie('push_monkey_banner_counter', 1, 365);            			
            		} else {

            			PushMonkeyWP.setCookie('push_monkey_banner_dismissed', 1, 365);
            		}
            	}
            }
		});
	}

	$(document).ready(function () {

		if (!PushMonkeyWP.getCookie('push_monkey_banner_dismissed')) {

			generate_banner('success');
		}
	});
});
