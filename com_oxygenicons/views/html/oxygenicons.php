<?php
/**
 * A view to load the Oxygen icons.
 *
 * @package Components\oxygenicons
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	pines.loadcss("<?php e($_->config->location); ?>components/com_oxygenicons/includes/oxygen/icons.css");
</script>