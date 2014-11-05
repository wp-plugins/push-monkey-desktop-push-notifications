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
	permission = window.safari.pushNotification.permission(PushMonkeyWPConfig.websiteID).permission;
	if(permission == 'default') {
		this.register();
	} else {
		PushMonkeyWPLog.store('Already registered or rejected.');
	}
}
PushMonkeyWP.check();
