<?php
/**
 * Add product actions for com_sales.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

// Product actions to add points to a customer's profile.
if ($_->config->com_customer->com_sales && !$_->depend->check('component', 'com_sales'))
	$_->config->com_customer->com_sales = false;

if (!$_->config->com_customer->com_sales)
	return;

/**
 * Shortcut to $_->com_customer->product_action_add_points().
 *
 * This prevents the class from being loaded every script run.
 *
 * @param array &$array The details array.
 * @return mixed The method's return value.
 */
function com_customer__product_action_add_points(&$array) {
	global $_;
	return call_user_func(array($_->com_customer, 'product_action_add_points'), $array);
}
/**
 * Shortcut to $_->com_customer->product_action_add_member_days().
 *
 * This prevents the class from being loaded every script run.
 *
 * @param array &$array The details array.
 * @return mixed The method's return value.
 */
function com_customer__product_action_add_member_days(&$array) {
	global $_;
	return call_user_func(array($_->com_customer, 'product_action_add_member_days'), $array);
}

$_->config->com_sales->product_actions[] = array(
	'type' => array('sold', 'voided'),
	'name' => 'com_customer/add_points',
	'cname' => 'Add Points',
	'description' => 'Add points to the customer\'s profile based on the price and the lookup table in configuration.',
	'callback' => 'com_customer__product_action_add_points'
);
foreach($_->config->com_customer->pointvalues as $cur_value) {
	$_->config->com_sales->product_actions[] = array(
		'type' => array('sold', 'voided'),
		'name' => "com_customer/add_points_$cur_value",
		'cname' => "Add $cur_value Points",
		'description' => "Add $cur_value points to the customer's profile.",
		'callback' => 'com_customer__product_action_add_points'
	);
}
unset ($cur_value);
foreach($_->config->com_customer->membervalues as $cur_value) {
	$_->config->com_sales->product_actions[] = array(
		'type' => array('sold', 'voided'),
		'name' => "com_customer/add_member_days_$cur_value",
		'cname' => "Add $cur_value Member Days",
		'description' => "Add $cur_value days of membership to the customer's profile. (Make them a member if they aren't one.)",
		'callback' => 'com_customer__product_action_add_member_days'
	);
}
unset ($cur_value);