<?php
/**
 * Displays a welcome note to the user.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Welcome to '.h($pines->config->system_name);
$this->note = 'You are now registered and logged in.';
?>
<div>
	<?php e($pines->config->com_user->reg_message_welcome); ?>
</div>