<?php
/**
 * A view to load PGrid.
 *
 * @package Components\pgrid
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
	pines.loadcss("<?php e($_->config->location); ?>components/com_pgrid/includes/jquery.pgrid.<?php e($_->config->com_pgrid->styling); ?>.css");
	pines.loadcss("<?php e($_->config->location); ?>components/com_pgrid/includes/jquery.pgrid.<?php e($_->config->com_pgrid->styling); ?>.icons.css");
	pines.loadjs("<?php e($_->config->location); ?>components/com_pgrid/includes/<?php echo $_->config->debug_mode ? 'jquery.pgrid.js' : 'jquery.pgrid.min.js'; ?>");
	pines.load(function(){
		$.fn.pgrid.defaults.pgrid_toolbar_target = "<?php echo stripslashes($_->config->com_pgrid->toolbar_target); ?>";
	});
</script>