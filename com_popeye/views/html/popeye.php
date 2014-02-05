<?php
/**
 * A view to load Popeye.
 *
 * @package Components\popeye
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	$_.loadcss("<?php e($_->config->location); ?>components/com_popeye/includes/jquery.popeye.css");
	$_.loadcss("<?php e($_->config->location); ?>components/com_popeye/includes/jquery.popeye.style.css");
	$_.loadjs("<?php e($_->config->location); ?>components/com_popeye/includes/<?php echo $_->config->debug_mode ? 'jquery.popeye.js' : 'jquery.popeye.min.js'; ?>");
</script>