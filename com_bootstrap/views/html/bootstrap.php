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
?>
<script type="text/javascript">
$_.loadcss("<?php e($_->config->location); ?>components/com_bootstrap/includes/themes/<?php e(clean_filename($_->config->com_bootstrap->theme)); ?>/css/<?php echo $_->config->debug_mode ? 'bootstrap.css' : 'bootstrap.min.css'; ?>");
<?php if ($_->config->com_bootstrap->responsive && file_exists('components/com_bootstrap/includes/themes/'.clean_filename($_->config->com_bootstrap->theme).'/css/'.($_->config->debug_mode ? 'bootstrap-responsive.css' : 'bootstrap-responsive.min.css'))) { ?>
$_.loadcss("<?php e($_->config->location); ?>components/com_bootstrap/includes/themes/<?php e(clean_filename($_->config->com_bootstrap->theme)); ?>/css/<?php echo $_->config->debug_mode ? 'bootstrap-responsive.css' : 'bootstrap-responsive.min.css'; ?>");
<?php } ?>
$_.loadcss("<?php e($_->config->rela_location); ?>components/com_bootstrap/includes/fontawesome/css/font-awesome.css");
$_.loadjs("<?php e($_->config->location); ?>components/com_bootstrap/includes/themes/<?php e(clean_filename($_->config->com_bootstrap->theme)); ?>/js/<?php echo $_->config->debug_mode ? 'bootstrap.js' : 'bootstrap.min.js'; ?>");
// Get the current number of columns in the CSS grid.
$_.com_bootstrap_get_columns = function(){
	var cur_grid = 0, cur_test;
	do {
		cur_grid++;
		cur_test = $("<div class=\"row\"><div class=\"span"+cur_grid+"\"><\/div><\/div>");
	} while (cur_grid <= 256 && cur_test.children().css("width") != "0px");
	cur_grid--;
	return cur_grid;
};
<?php /* Example:
$_(function(){
	alert($_.com_bootstrap_get_columns());
});
*/ ?>
</script>
<noscript>
<link href="<?php e($_->config->location); ?>components/com_bootstrap/includes/themes/<?php e(clean_filename($_->config->com_bootstrap->theme)); ?>/css/<?php echo $_->config->debug_mode ? 'bootstrap.css' : 'bootstrap.min.css'; ?>" media="all" rel="stylesheet" type="text/css" />
<?php if ($_->config->com_bootstrap->responsive && file_exists('components/com_bootstrap/includes/themes/'.clean_filename($_->config->com_bootstrap->theme).'/css/'.($_->config->debug_mode ? 'bootstrap-responsive.css' : 'bootstrap-responsive.min.css'))) { ?>
<link href="<?php e($_->config->location); ?>components/com_bootstrap/includes/themes/<?php e(clean_filename($_->config->com_bootstrap->theme)); ?>/css/<?php echo $_->config->debug_mode ? 'bootstrap-responsive.css' : 'bootstrap-responsive.min.css'; ?>" media="all" rel="stylesheet" type="text/css" />
<?php } ?>
<link href="<?php e($_->config->rela_location); ?>components/com_bootstrap/includes/fontawesome/css/font-awesome.css" media="all" rel="stylesheet" type="text/css" />
</noscript>
<!--[if IE 7]>
<script type="text/javascript">
$_.loadcss("<?php e($_->config->rela_location); ?>components/com_bootstrap/includes/fontawesome/css/font-awesome-ie7.min.css");
</script>
<![endif]-->
<style type="text/css">
select, textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input {
margin-bottom: 0;
width: auto;
}
</style>