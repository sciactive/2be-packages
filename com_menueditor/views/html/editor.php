<?php
/**
 * A view to load the menu editor.
 *
 * @package Components\menueditor
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$_->icons->load();
$_->com_pgrid->load();
$_->com_jstree->load();
?>
<script type="text/javascript">
	$_.loadcss("<?php e($_->config->location); ?>components/com_menueditor/includes/jquery.menueditor.css");
	$_.loadjs("<?php e($_->config->location); ?>components/com_menueditor/includes/jquery.menueditor.js");
	$_.com_menueditor_dialog_url = <?php echo json_encode(pines_url('com_menueditor', 'entry/dialog')); ?>;
</script>