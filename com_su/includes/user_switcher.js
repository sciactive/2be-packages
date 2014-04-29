$_(function(){
	var notice;
	$.ajax({
		url: $_.com_su_loginpage_url,
		type: "GET",
		dataType: "html",
		beforeSend: function(){
			notice = new PNotify({
				text: "Loading login page...",
				title: "Switch User",
				icon: "picon picon-throbber",
				hide: false,
				width: "350px",
				history: {
					history: false
				}
			});
		},
		error: function(XMLHttpRequest, textStatus){
			notice.remove();
			$_.error("An error occured while trying to load login page:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
		},
		success: function(data){
			notice.update({
				title: "Switch User",
				text: data,
				icon: "picon picon-dialog-password",
				insert_brs: false
			}).get().find("input").eq(0).focus();
		}
	});
});