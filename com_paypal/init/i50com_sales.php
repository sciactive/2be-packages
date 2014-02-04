<?php
/**
 * Add processing type for com_sales.
 *
 * @package Components\paypal
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * Shortcut to $pines->com_paypal->payment_credit().
 *
 * This prevents the class from being loaded every script run.
 * 
 * @param array &$array The argument array.
 * @return mixed The method's return value.
 */
function com_paypal__payment_credit(&$array) {
	global $pines;
	return call_user_func(array($pines->com_paypal, 'payment_credit'), $array);
}

$pines->config->com_sales->processing_types[] = array(
	'name' => 'com_paypal/credit',
	'cname' => 'Credit Card',
	'description' => 'Process credit card payment using Authorize.net.',
	'callback' => 'com_paypal__payment_credit'
);