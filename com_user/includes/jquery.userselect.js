/*
 * jQuery User Select (customerselect) Plugin 1.0.0
 *
 * Copyright (c) 2010 Angela Murrell
 *
 * Licensed (along with all of 2be) under the GNU Affero GPL:
 *	  http://www.gnu.org/licenses/agpl.html
 */

(function($) {
$.fn.userselect = function(options){
// Iterate and transform each matched element.
var all = this;
all.each(function(){
	var us = $(this);
	us.userselect_version = "1.0.0";
	// Check for the userselect class. If it has it, we've already transformed this element.
	if (us.hasClass("ui-userselect")) return true;
	// Add the customerselect class.
	us.addClass("ui-userselect");
	var opts = {
		minLength: 2,
		source: function(request, response) {
			$.ajax({
				url: $_.com_user_autouser_url,
				dataType: "json",
				data: {"q": request.term},
				success: function(data) {
					if (!data) {
						response([]);
						return;
					}
					response($.map(data, function(item) {
						return {
							"id": $_.safe(item.guid),
							"label": $_.safe(item.name),
							"value": item.guid+": "+item.name,
							"desc": "<em><pre>"+(item.email ? " "+$_.safe(item.email) : "")+
								(item.phone_cell ? " "+$_.safe(item.phone_cell) :
								(item.phone_home ? " "+$_.safe(item.phone_home) :
								(item.phone_work ? " "+$_.safe(item.phone_work) : "")))+"</pre></em>"
						};
					}));
				}
			});
		}
	};
	if (typeof options != "undefined")
		$.extend(opts, options);
	us.autocomplete(opts).data("autocomplete")._renderItem = function(ul, item){
		return $("<li></li>").data("item.autocomplete", item)
			.append("<a><strong>"+item.label+"</strong><br />"+item.desc+"</a>")
			.appendTo(ul);
	};
	// Save the userselect object in the DOM, so we can access it.
	this._userselect = us;
});
return all;
};
})(jQuery);