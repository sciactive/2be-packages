/*
 * jQuery Company Select (companyselect) Plugin 1.0.0
 *
 * Copyright (c) 2010 Hunter Perrin
 *
 * Licensed (along with all of 2be) under the GNU Affero GPL:
 *	  http://www.gnu.org/licenses/agpl.html
 */

(function($) {
$.fn.companyselect = function(options){
// Iterate and transform each matched element.
var all = this;
all.each(function(){
	var cs = $(this);
	cs.companyselect_version = "1.0.0";
	// Check for the companyselect class. If it has it, we've already transformed this element.
	if (cs.hasClass("ui-companyselect")) return true;
	// Add the companyselect class.
	cs.addClass("ui-companyselect");
	var opts = {
		minLength: 2,
		source: function(request, response) {
			$.ajax({
				url: $_.com_customer_autocompany_url,
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
								(item.phone ? " "+$_.safe(item.phone) : "")+
								(item.website ? " "+$_.safe(item.website) : "")+"</pre></em>"
						};
					}));
				}
			});
		}
	};
	if (typeof options != "undefined")
		$.extend(opts, options);
	cs.autocomplete(opts).data("autocomplete")._renderItem = function(ul, item){
		return $("<li></li>").data("item.autocomplete", item)
			.append("<a><strong>"+item.label+"</strong><br />"+item.desc+"</a>")
			.appendTo(ul);
	};
	// Save the companyselect object in the DOM, so we can access it.
	this._companyselect = cs;
});
return all;
};
})(jQuery);