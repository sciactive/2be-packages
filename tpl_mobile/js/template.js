$_(function(){
	var stack_bottomright = {"dir1": "up", "dir2": "left", "firstpos1": 10, "firstpos2": 10};
	if (typeof PNotify !== "undefined") {
		PNotify.prototype.options.opacity = 1;
		PNotify.prototype.options.stack = stack_bottomright;
		PNotify.prototype.options.addclass = 'stack-bottomright';
		PNotify.prototype.options.nonblock.nonblock = false;
		PNotify.prototype.options.buttons.closer_hover = false;
		PNotify.prototype.options.buttons.sticker_hover = false;
		PNotify.prototype.options.history.history = false;
	}
	if ($.fn.pgrid)
		$.fn.pgrid.defaults.pgrid_stateful_height = false;

	// Close the menu if the page is clicked while the menu is open.
	$("body").on("click", "#wrapper.menu-open #page, #wrapper.sidebar-open #page", function(){
		$("#wrapper").removeClass('menu-open sidebar-open');
	});
});