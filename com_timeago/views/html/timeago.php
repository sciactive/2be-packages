<?php
/**
 * A view to load PSteps.
 *
 * @package Components\psteps
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	pines.loadjs("<?php e($_->config->location); ?>components/com_timeago/includes/<?php echo $_->config->debug_mode ? 'jquery.timeago.js' : 'jquery.timeago.min.js'; ?>");
</script>