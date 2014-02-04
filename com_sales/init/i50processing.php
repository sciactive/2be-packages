<?php
/**
 * Add the default processing types.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * Shortcut to $_->com_sales->payment_instant().
 *
 * This prevents the class from being loaded every script run.
 *
 * @param array &$array The argument array.
 * @return mixed The method's return value.
 */
function com_sales__payment_instant(&$array) {
	global $_;
	return call_user_func(array($_->com_sales, 'payment_instant'), $array);
}
/**
 * Shortcut to $_->com_sales->payment_manager().
 *
 * This prevents the class from being loaded every script run.
 *
 * @param array &$array The argument array.
 * @return mixed The method's return value.
 */
function com_sales__payment_manager(&$array) {
	global $_;
	return call_user_func(array($_->com_sales, 'payment_manager'), $array);
}

$_->config->com_sales->processing_types[] = array(
	'name' => 'com_sales/instant',
	'cname' => 'Instant Processing',
	'description' => 'Approval and processing happen immediately. For example, a cash transaction.',
	'callback' => 'com_sales__payment_instant'
);
$_->config->com_sales->processing_types[] = array(
	'name' => 'com_sales/manager',
	'cname' => 'Manager Approval',
	'description' => 'Approval happens only after a manager verifies the payment. For example, a large cash transaction.',
	'callback' => 'com_sales__payment_manager'
);