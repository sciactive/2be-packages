<?php
/**
 * A view to load jQuery UI.
 *
 * @package Components\jquery
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	$_.loadcss("<?php e($_->config->location); ?>components/com_jquery/includes/jquery-ui/<?php e($_->config->com_jquery->theme); ?>/jquery-ui.css");
	$_.loadjs("<?php e($_->config->location); ?>components/com_jquery/includes/<?php echo $_->config->debug_mode ? 'jquery-ui-1.8.21.js' : 'jquery-ui-1.8.21.min.js'; ?>");
	$_.load(function(){
	// This allows to use jquitabs, jquibutton. (In case of name conflict, like Bootstrap.)
	$.widget.bridge('jquitabs', $.ui.tabs);
	$.widget.bridge('jquibutton', $.ui.button);
	<?php if (isset($_->com_bootstrap)) { ?>
	// And this fixes buttons in dialogs using Bootstrap.
	var real_dialog = $.fn.dialog;
	$.fn.dialog = function(){
		var d = real_dialog.apply(this, arguments);
		if (typeof d == "object" && d.jquery && d.hasClass("ui-dialog-content"))
			real_dialog.call(d, "widget").find(".ui-dialog-buttonpane button").addClass("btn btn-default");
		return d;
	};
	<?php } ?>
	});
</script>