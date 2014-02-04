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
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	pines.loadcss("<?php e($_->config->location); ?>components/com_ptags/includes/jquery.ptags.default.css");
	pines.loadjs("<?php e($_->config->location); ?>components/com_ptags/includes/<?php echo $_->config->debug_mode ? 'jquery.ptags.js' : 'jquery.ptags.min.js'; ?>");
</script>