<?php
/**
 * A function to load the timeout notice.
 *
 * @package Components\timeoutnotice
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	pines.com_timeoutnotice = {
		"check_url": <?php echo json_encode(pines_url('com_timeoutnotice', 'check')); ?>,
		"login_url": <?php echo json_encode(pines_url('com_timeoutnotice', 'login')); ?>,
		"loginpage_url": <?php echo json_encode(pines_url('com_timeoutnotice', 'loginpage')); ?>,
		"extend_url": <?php echo json_encode(pines_url('com_timeoutnotice', 'extend')); ?>,
		"action": <?php echo json_encode($_->config->com_timeoutnotice->action); ?>,
		"redirect_url": <?php echo json_encode($_->config->com_timeoutnotice->redirect_url); ?>
	};
	pines.loadjs("<?php e($_->config->location); ?>components/com_timeoutnotice/includes/timeout_notice.js", true);
</script>