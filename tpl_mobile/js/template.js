$_(function(){
	if ($.pnotify) {
		PNotify.prototype.options.opacity = 1;
		PNotify.prototype.options.nonblock.nonblock = false;
		PNotify.prototype.options.buttons.closer_hover = false;
		PNotify.prototype.options.buttons.sticker_hover = false;
		PNotify.prototype.options.history.history = false;
	}
	if ($.fn.pgrid)
		$.fn.pgrid.defaults.pgrid_stateful_height = false;

	// Menu link.
	var wrapper = $("#wrapper"),
		menu = $("#menu"),
		page = $("#page");
	$("#menu_link").click(function(){
		if (wrapper.hasClass("menu_open")) {
			menu.animate({
				right: "100%",
				left: "-85%"
			}, 250, function(){
				menu.css("min-height", "100%");
			});
			page.animate({
				left: "0"
			}, 250);
		} else {
			menu.css("min-height", ($("body").height() - 50)+"px").animate({
				right: "15%",
				left: "0"
			}, 250);
			page.animate({
				left: "85%"
			}, 250);
		}
		wrapper.toggleClass("menu_open");
	});
	// Close the menu if the page is clicked while the menu is open.
	$("body").on("click", ".menu_open #page", function(){
		$("#menu_link").click();
	});
	// Menus.
	$(".menu").on("click", "a.expander", function(){
		$(this).toggleClass("btn-success").children().toggleClass("fa-chevron-down fa-chevron-up").end().closest("li").children("ul").toggle();
	}).on("click", "a:not(.expander)[href=javascript:void(0);]", function(){
		$(this).siblings("a.expander").click();
	});
});