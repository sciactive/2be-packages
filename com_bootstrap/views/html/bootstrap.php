<?php
/**
 * A view to load Bootstrap CSS.
 *
 * @package Pines
 * @subpackage com_bootstrap
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	pines.loadcss("<?php echo htmlspecialchars($pines->config->location); ?>components/com_bootstrap/includes/themes/<?php echo htmlspecialchars(clean_filename($pines->config->com_bootstrap->theme)); ?>/css/<?php echo $pines->config->debug_mode ? 'bootstrap.css' : 'bootstrap.min.css'; ?>");
	<?php if ($pines->config->com_bootstrap->responsive && file_exists('components/com_bootstrap/includes/themes/'.clean_filename($pines->config->com_bootstrap->theme).'/css/'.($pines->config->debug_mode ? 'bootstrap-responsive.css' : 'bootstrap-responsive.min.css'))) { ?>
	pines.loadcss("<?php echo htmlspecialchars($pines->config->location); ?>components/com_bootstrap/includes/themes/<?php echo htmlspecialchars(clean_filename($pines->config->com_bootstrap->theme)); ?>/css/<?php echo $pines->config->debug_mode ? 'bootstrap-responsive.css' : 'bootstrap-responsive.min.css'; ?>");
	<?php } ?>
	pines.loadjs("<?php echo htmlspecialchars($pines->config->location); ?>components/com_bootstrap/includes/themes/<?php echo htmlspecialchars(clean_filename($pines->config->com_bootstrap->theme)); ?>/js/<?php echo $pines->config->debug_mode ? 'bootstrap.js' : 'bootstrap.min.js'; ?>");
	// Get the current number of columns in the CSS grid.
	pines.com_bootstrap_get_columns = function(){
		var cur_grid = 0, cur_test;
		do {
			cur_grid++;
			cur_test = $("<div class=\"row\"><div class=\"span"+cur_grid+"\"><\/div><\/div>");
		} while (cur_grid <= 256 && cur_test.children().css("width") != "0px");
		cur_grid--;
		return cur_grid;
	};
	<?php /* Example:
	pines(function(){
		alert(pines.com_bootstrap_get_columns());
	});
	*/ ?>
</script>
<style type="text/css">
	input, textarea, select {
		margin-bottom: 0;
		width: auto;
	}
</style>