$_.entity_helper = function(e) {
	var elem = $(e),
		guid = elem.attr("data-entity"),
		context = elem.attr("data-entity-context"),
		modal = $('<div class="modal"><div class="modal-header"><button class="close" data-dismiss="modal">×</button><h3>Loading...</h3></div><div class="modal-body"><p style="height: 32px; background-repeat: no-repeat; background-position: center;" class="picon-32 picon-throbber"></p></div><div class="modal-footer"><a href="javascript:void(0);" class="btn" data-dismiss="modal">Cancel</a></div></div>');
	modal.modal();
	var header = modal.find(".modal-header"),
		body = modal.find(".modal-body"),
		footer = modal.find(".modal-footer");
	$.ajax({
		url: $_.entity_helper_url,
		type: "POST",
		dataType: "json",
		data: {"id": guid, "context": context},
		error: function(XMLHttpRequest, textStatus){
			modal.modal('hide');
			$_.error("An error occured:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
		},
		success: function(data){
			if (!data) {
				modal.modal('hide');
				$_.error("An error occured.");
				return;
			}
			$_.pause();
			if (data.title)
				header.find("h3").text(data.title);
			else
				header.find("h3").text("Entity "+$_.safe(guid)+" ("+$_.safe(context)+")");
			body.html(data.body);
			footer.html(data.footer);
			$_.play();
		}
	});
};