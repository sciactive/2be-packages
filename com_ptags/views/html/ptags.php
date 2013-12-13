<?php
/**
 * A view to load PTags.
 *
 * @package Components\ptags
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	pines.loadcss("<?php echo htmlspecialchars($pines->config->location); ?>components/com_ptags/includes/jquery.ptags.default.css");
	pines.loadjs("<?php echo htmlspecialchars($pines->config->location); ?>components/com_ptags/includes/<?php echo $pines->config->debug_mode ? 'jquery.ptags.js' : 'jquery.ptags.min.js'; ?>");
</script>