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
?>
<script type="text/javascript">
$_.loadcss("<?php e($_->config->location); ?>components/com_pnotify/includes/jquery.pnotify.default.css");
$_.loadcss("<?php e($_->config->location); ?>components/com_pnotify/includes/jquery.pnotify.default.icons.css");
$_.loadjs("<?php e($_->config->location); ?>components/com_pnotify/includes/<?php echo $_->config->debug_mode ? 'jquery.pnotify.js' : 'jquery.pnotify.min.js'; ?>");
$_.pnotify_alert_defaults = {nonblock: true};
$_.pnotify_notice_defaults = {nonblock: true};
$_.pnotify_error_defaults = {type: "error", hide: false, nonblock: false};
$_.load(function(){
	if (!window._alert) {
		window._alert = window.alert;
		window.alert = function(message){
			var options = $.extend({title: "Alert", text: $_.safe(message)}, $_.pnotify_alert_defaults);
			return $.pnotify(options);
		};
		$_.notice = function(message, title){
			var options = $.extend({title: title ? title : "Notice", text: String(message)}, $_.pnotify_notice_defaults);
			return $.pnotify(options);
		};
		$_.error = function(message, title){
			var options = $.extend({title: title ? title : "Error", text: String(message)}, $_.pnotify_error_defaults);
			return $.pnotify(options);
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