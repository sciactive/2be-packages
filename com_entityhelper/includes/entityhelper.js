$_.entity_helper = function(e) {
	var elem = $(e),
		guid = elem.attr("data-entity"),
		context = elem.attr("data-entity-context"),
		modal = $('<div class="modal fade"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Loading...</h4></div><div class="modal-body"><p style="height: 32px; background-repeat: no-repeat; background-position: center;" class="picon-32 picon-throbber"></p></div><div class="modal-footer"><a href="javascript:void(0);" class="btn" data-dismiss="modal">Cancel</a></div></div></div></div>');
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
				header.find(".modal-title").text(data.title);
			else
				header.find(".modal-title").text("Entity "+$_.safe(guid)+" ("+$_.safe(context)+")");
			body.html(data.body);
			footer.html(data.footer);
			$_.play();
		}
	});
};