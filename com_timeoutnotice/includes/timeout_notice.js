$_(function(){
	var login_page = function(){
		var notice;
		$.ajax({
			url: $_.com_timeoutnotice.loginpage_url,
			type: "GET",
			dataType: "html",
			beforeSend: function(){
				notice = $.pnotify({
					text: "Loading login page...",
					title: "Login",
					icon: "picon picon-throbber",
					hide: false,
					history: false
				});
			},
			error: function(XMLHttpRequest, textStatus){
				notice.pnotify_remove();
				$_.error("An error occured while trying to load login page:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
			},
			success: function(data){
				notice.pnotify_remove();
				$_.pause();
				var login_dialog = $("<div />").html(data+"<br />").dialog({
					modal: true,
					resizable: false,
					title: "Login",
					width: 450,
					close: function(){
						interval = setInterval(check_timeout, 120000);
						check_timeout();
					},
					buttons: {
						"Login": function(){
							$.ajax({
								url: $_.com_timeoutnotice.login_url,
								type: "POST",
								dataType: "json",
								data: login_dialog.find(".com_timeoutnotice_login_form").serialize(),
								error: function(XMLHttpRequest, textStatus){
									$_.error("An error occured while trying to login:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
								},
								success: function(data){
									if (!data) {
										alert("Login failed.");
										return;
									}
									login_dialog.dialog("close").remove();
								}
							});
						}
					}
				});
				$_.play();
				login_dialog.find(".already_loggedin").click(function(){
					login_dialog.dialog("close").remove();
				}).end().find(".com_timeoutnotice_login_form").submit(function(){
					login_dialog.dialog("option", "buttons").Login();
					return false;
				}).find("input").keydown(function(e){
					if (e.keyCode == 13)
						login_dialog.dialog("option", "buttons").Login();
				}).eq(0).focus();
			}
		});
	};

	var logged_out = function(){
		if (interval)
			clearInterval(interval);
		else
			return;
		interval = false;
		switch ($_.com_timeoutnotice.action) {
			case "dialog":
			default:
				login_page();
				break;
			case "refresh":
				location.reload(true);
				break;
			case "redirect":
				$_.get($_.com_timeoutnotice.redirect_url);
				break;
		}
	}

	var session_notice;
	var timeout;
	var check_timeout = function(){
		$.ajax({
			url: $_.com_timeoutnotice.check_url,
			type: "GET",
			dataType: "json",
			success: function(data){
				if (!data) {
					if (session_notice)
						session_notice.pnotify_remove();
					logged_out();
					return;
				}
				if (data > 60) {
					if (timeout)
						clearTimeout(timeout);
					if (session_notice && session_notice.is(":visible"))
						session_notice.pnotify_remove();
				}
				if (data < 260) {
					timeout = setTimeout(function(){
						setTimeout(check_timeout, 21000);
						setTimeout(check_timeout, 41000);
						setTimeout(check_timeout, 61000);
						if (session_notice) {
							if (!session_notice.is(":visible"))
								session_notice.pnotify_display();
						} else {
							session_notice = $.pnotify({
								title: "Session Timeout",
								text: "Your session is about to expire. <a href=\"javascript:void(0)\" class=\"extend_session\">Click here to stay logged in.</a>",
								icon: "picon picon-user-away",
								hide: false,
								history: false,
								mouse_reset: false
							});
							session_notice.find("a.extend_session").click(function(){
								$.ajax({
									url: $_.com_timeoutnotice.extend_url,
									type: "GET",
									dataType: "json",
									error: function(XMLHttpRequest, textStatus){
										$_.error("An error occured while trying to extend your session:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
									},
									success: function(data){
										session_notice.pnotify_remove();
										if (!data) {
											logged_out();
											return;
										}
										alert("Your session has been extended.");
									}
								});
							});
						}
					}, (data - 60) * 1000);
				}
			}
		});
	};

	var interval = setInterval(check_timeout, 120000);
});