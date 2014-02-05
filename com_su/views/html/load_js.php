<?php
/**
 * A function to load the user switcher.
 *
 * @package Components\su
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	function com_su_load_switcher() {
		$_.com_su_loginpage_url = <?php echo json_encode(pines_url('com_su', 'loginpage')); ?>;
		$_.loadjs("<?php e($_->config->location); ?>components/com_su/includes/user_switcher.js", true);
	}
</script>