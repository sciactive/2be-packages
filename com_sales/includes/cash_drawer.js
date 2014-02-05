$_(function(){
	if ($.browser.msie)
		return;
	var callbacks = [];
	var drawer_opened_properly = false;
	var drawer_message = null;

	// The modal dialog.
	var dialog = $("<div title=\"Cash Drawer\" />").append($("<p />", {"class": "dialog_text"})).dialog({
		bgiframe: true,
		autoOpen: false,
		modal: true,
		width: 600
	});

	$_.drawer_not_supported = function(event){
		var current = callbacks.slice();
		callbacks = [];
		$.each(current, function(index, value){
			value("not_supported");
		});
		setTimeout($_.drawer_check, 5000);
	};
	window.addEventListener("pines_cash_drawer_not_supported", $_.drawer_not_supported, false);
	$_.drawer_error = function(event){
		var current = callbacks.slice();
		callbacks = [];
		$.each(current, function(index, value){
			value("error");
		});
		setTimeout($_.drawer_check, 5000);
	};
	window.addEventListener("pines_cash_drawer_error", $_.drawer_error, false);
	$_.drawer_is_closed = function(event){
		if (dialog.dialog("isOpen")) {
			dialog.dialog("close");
			drawer_message = null;
		}
		drawer_opened_properly = false;
		var current = callbacks.slice();
		callbacks = [];
		$.each(current, function(index, value){
			value("is_closed");
		});
		setTimeout($_.drawer_check, 5000);
	};
	window.addEventListener("pines_cash_drawer_is_closed", $_.drawer_is_closed, false);
	$_.drawer_is_open = function(event){
		if (!dialog.dialog("isOpen")) {
			if (drawer_message == null)
				drawer_message = "Close the cash drawer when you are finished in order to continue.";
			dialog.find("p.dialog_text").html(
				drawer_opened_properly ?
					"<span style=\"font-size: 2em;\">"+$_.safe(drawer_message)+"</span>" :
					"<span style=\"font-size: 2em; color: red;\">The cash drawer has been opened without authorization. Close the cash drawer immediately. Corporate has been notified and the incident has been logged.</span>"
			);
			if (!drawer_opened_properly) {
				// notify manager
			}
			dialog.dialog("open");
		}
		var current = callbacks.slice();
		callbacks = [];
		$.each(current, function(index, value){
			value("is_open");
		});
		setTimeout($_.drawer_check, 5000);
	};
	window.addEventListener("pines_cash_drawer_is_open", $_.drawer_is_open, false);
	$_.drawer_not_found = function(event){
		if (!dialog.dialog("isOpen")) {
			dialog.find("p.dialog_text").html(
				"<span style=\"font-size: 2em; color: red;\">The cash drawer has been disconnected. Reconnect the cash drawer immediately. Corporate has been notified and the incident has been logged.</span>"
			);
			if (!drawer_opened_properly) {
				// notify manager
			}
			dialog.dialog("open");
		}
		var current = callbacks.slice();
		callbacks = [];
		$.each(current, function(index, value){
			value("not_found");
		});
		setTimeout($_.drawer_check, 5000);
	};
	window.addEventListener("pines_cash_drawer_not_found", $_.drawer_not_found, false);
	$_.drawer_misconfigured = function(event){
		var current = callbacks.slice();
		callbacks = [];
		$.each(current, function(index, value){
			value("misconfigured");
		});
		setTimeout($_.drawer_check, 5000);
	};
	window.addEventListener("pines_cash_drawer_misconfigured", $_.drawer_misconfigured, false);
	$_.drawer_check = function(callback){
		if ($.isFunction(callback))
			$.merge(callbacks, [callback]);
		var evt = document.createEvent("Events");
		evt.initEvent("pines_cash_drawer_check", true, false);
		window.dispatchEvent(evt);
	};
	$_.drawer_open = function(callback, message){
		drawer_opened_properly = true;
		if (message != undefined)
			drawer_message = message;
		if ($.isFunction(callback))
			$.merge(callbacks, [callback]);
		var evt = document.createEvent("Events");
		evt.initEvent("pines_cash_drawer_open", true, false);
		window.dispatchEvent(evt);
	};

	setTimeout($_.drawer_check, 5000);
});