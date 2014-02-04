<?php
/**
 * A view to load jQuery.
 *
 * @package Components\jquery
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	pines.loadjs("<?php e($pines->config->location); ?>components/com_jquery/includes/<?php echo $pines->config->debug_mode ? 'jquery-1.7.2.js' : 'jquery-1.7.2.min.js'; ?>");
</script>