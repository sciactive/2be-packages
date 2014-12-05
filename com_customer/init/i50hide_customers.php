<?php
/**
 * Hide customers when viewing user list.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_customer->hide_customers)
	return;

/**
 * Add hook to hide customers.
 *
 * @param array &$args The arguments.
 * @param string $name Name of the hook.
 */
function com_customer__hook_entities(&$args, $name) {
	// If the request is for all users, don't hide customers.
	if ($name == '$_->user_manager->get_users' && $args[0])
		return;
	global $_;
	$_->info->com_customer->hook_callbacks = $_->hook->add_callback('$_->nymph->getEntities', -10, 'com_customer__hide_customers');
}
/**
 * Hide customers from com_user.
 *
 * @param array &$args The arguments.
 */
function com_customer__hide_customers(&$args) {
	global $_;
	if ($args[0]['class'] == user) {
		$args[] = array('!&',
				'tag' => array('com_customer', 'customer')
			);
		$_->hook->del_callback_by_id('$_->nymph->getEntities', $_->info->com_customer->hook_callbacks[0]);
	}
}

$_->hook->add_callback('$_->user_manager->list_users', -10, 'com_customer__hook_entities');
$_->hook->add_callback('$_->user_manager->get_users', -10, 'com_customer__hook_entities');