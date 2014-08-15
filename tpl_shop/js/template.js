$_(function(){
	var stack_bottomright = {"dir1": "up", "dir2": "left", "firstpos1": 25, "firstpos2": 25};
	if (typeof PNotify !== "undefined") {
		PNotify.prototype.options.opacity = .9;
		PNotify.prototype.options.stack = stack_bottomright;
		PNotify.prototype.options.addclass = 'stack-bottomright';
		PNotify.prototype.options.delay = 15000;
	}
});