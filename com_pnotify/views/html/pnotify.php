<?php
/**
 * A view to load PNotify.
 *
 * @package Components\pnotify
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$_->icons->load();
$min = $_->config->debug_mode ? '' : '.min';
?>
<script type="text/javascript">
$_.loadcss("<?php e($_->config->location); ?>components/com_pnotify/includes/pnotify.custom<?php e($min); ?>.css");
$_.loadcss("<?php e($_->config->location); ?>components/com_pnotify/includes/pnotify.picon<?php e($min); ?>.css");
$_.loadjs("<?php e($_->config->location); ?>components/com_pnotify/includes/pnotify.custom<?php e($min); ?>.js");
$_.pnotify_alert_defaults = {nonblock: {nonblock: true}};
$_.pnotify_notice_defaults = {nonblock: {nonblock: true}};
$_.pnotify_error_defaults = {type: "error", hide: false, nonblock: {nonblock: false}};
$_.load(function(){
	if (!window._alert) {
		window._alert = window.alert;
		window.alert = function(message){
			var options = $.extend({title: "Alert", text: $_.safe(message)}, $_.pnotify_alert_defaults);
			return new PNotify(options);
		};
		$_.notice = function(message, title){
			var options = $.extend({title: title ? title : "Notice", text: String(message)}, $_.pnotify_notice_defaults);
			return new PNotify(options);
		};
		$_.error = function(message, title){
			var options = $.extend({title: title ? title : "Error", text: String(message)}, $_.pnotify_error_defaults);
			return new PNotify(options);
		};
	}
});
</script>
<style type="text/css">
.ui-pnotify-history-pulldown {
width: 16px;
}
.ui-pnotify-closer span, .ui-pnotify-sticker span {
width: 16px;
display: inline-block;
}
</style>