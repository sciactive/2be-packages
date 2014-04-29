<?php
/**
 * A view to load Bootstrap CSS.
 *
 * @package Components\bootstrap
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

$themes = json_decode(file_get_contents('components/com_bootstrap/includes/css/themes.json'), true);
$theme = $themes[$_->config->com_bootstrap->theme];
unset($themes);

?>
<script type="text/javascript">
<?php if ($theme['andnormal']) { ?>
$_.loadcss("<?php e($_->config->location); ?>components/com_bootstrap/includes/css/<?php e($_->config->debug_mode ? 'bootstrap.css' : 'bootstrap.min.css'); ?>");
<?php } ?>
$_.loadcss("<?php e($_->config->location); ?>components/com_bootstrap/includes/css/<?php e(clean_filename($_->config->debug_mode ? $theme['file'] : $theme['minfile'])); ?>");
$_.loadcss("<?php e($_->config->rela_location); ?>components/com_bootstrap/includes/fontawesome/css/<?php echo $_->config->debug_mode ? 'font-awesome.css' : 'font-awesome.min.css'; ?>");
$_.loadjs("<?php e($_->config->location); ?>components/com_bootstrap/includes/js/<?php echo $_->config->debug_mode ? 'bootstrap.js' : 'bootstrap.min.js'; ?>");
// Get the current number of columns in the CSS grid.
$_.com_bootstrap_get_columns = function(){
var test, last, cur = 0;
do {
	if (test) last = test.children().css("width");
	cur++;
	test = $("<div class=\"row\"><div class=\"col-xs-"+cur+"\"><\/div><\/div>").appendTo("body");
} while (cur <= 256 && test.children().css("width") != last);
cur -= 2;
test.remove();
return cur;
};
<?php /* Example:
$_(function(){
	alert($_.com_bootstrap_get_columns());
});
*/ ?>
</script>
<style type="text/css">
select, textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input {
margin-bottom: 0;
width: auto;
}
.nav-tabs {
margin-bottom: 15px;
}
</style>