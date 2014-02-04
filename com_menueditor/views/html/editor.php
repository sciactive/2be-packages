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
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$pines->icons->load();
$pines->com_pgrid->load();
$pines->com_jstree->load();
?>
<script type="text/javascript">
	pines.loadcss("<?php e($pines->config->location); ?>components/com_menueditor/includes/jquery.menueditor.css");
	pines.loadjs("<?php e($pines->config->location); ?>components/com_menueditor/includes/jquery.menueditor.js");
	pines.com_menueditor_dialog_url = <?php echo json_encode(pines_url('com_menueditor', 'entry/dialog')); ?>;
</script>