<?php
/**
 * Create customer follow-ups.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_customer->com_calendar)
	return;

/**
 * Cancel any customer follow-ups for a canceled/voided sale.
 *
 * @param object &$object The sale/return being saved.
 */
function com_customer__cancel_appointments(&$object) {
	global $_;

	// Cancel any open customer follow-ups for this sale.
	$follow_ups = $_->nymph->getEntities(
			array('class' => com_customer_interaction),
			array('&',
				'data' => array('status', 'open'),
				'ref' => array('sale', $object)
			)
		);
	foreach ($follow_ups as $cur_appt) {
		$cur_appt->status = 'canceled';
		$cur_appt->review_comments[] = format_date(time(), 'custom', 'n/j/y g:iA').': Returned ('.ucwords($cur_appt->status).')';
		if ($_->config->com_customer->com_calendar) {
			$cur_appt->event->color = 'gainsboro';
			$cur_appt->event->information = $cur_appt->employee->name." (".ucwords($cur_appt->status).") \n";
			$cur_appt->event->information .= $cur_appt->comments."\n".implode("\n",$cur_appt->review_comments);
			$cur_appt->event->save();
		}
		$cur_appt->save();
	}
}

/**
 * Create customer follow-ups for any completed sale.
 *
 * @param array &$arguments Unused.
 * @param mixed $name Unused.
 * @param object &$object The sale being saved.
 */
function com_customer__check_sale(&$arguments, $name, &$object) {
	global $_;

	if (!is_object($object) || !$_->config->com_customer->follow_up)
		return;
	$websale = isset($object->user->guid) ? $object->user->is($object->customer) : (isset($_SESSION['user']->guid) ? $_SESSION['user']->is($object->customer) : true);
	if (!$object->followed_up && isset($object->customer->guid) && !$websale && $object->status == 'paid') {
		$totals = array();
		foreach($object->products as $cur_product) {
			if ($cur_product['returned_quantity'] >= $cur_product['quantity'])
				continue;
			if (!isset($totals[$cur_product['salesperson']->guid]))
				$totals[$cur_product['salesperson']->guid] = 0;
			$totals[$cur_product['salesperson']->guid] += ($cur_product['quantity'] * $cur_product['price']);
		}
		if (empty($totals))
			return;
		$sales_rep = com_hrm_employee::factory((int) array_search(max($totals), $totals));
		if (!isset($sales_rep->guid))
			return;
		// Check to see that the customer is actually saved using the customer class.
		if (isset($object->customer->guid) && !(is_a($object->customer, 'com_customer_customer') || is_a($object->customer, 'hook_override_com_customer_customer'))) {
			// Customer was saved as a different class. Try to load the customer using the customer class.
			$customer = com_customer_customer::factory($object->customer->guid);
			if (isset($customer->guid))
				$object->customer = $customer;
		}
		if (is_callable(array($object->customer, 'schedule_follow_up'))) {
			if (!$object->warehouse_items || $object->warehouse_complete) {
				$object->customer->schedule_follow_up($sales_rep, $object);
				$object->followed_up = true;
			} elseif (!$object->wh_followed_up) {
				$object->customer->schedule_follow_up($sales_rep, $object, true);
				$object->wh_followed_up = true;
			}
		}
	}
	if ($object->status == 'voided')
		com_customer__cancel_appointments($object);
}

$_->hook->add_callback('com_sales_sale->save', -10, 'com_customer__check_sale');

/**
 * Cancel customer follow-ups for any returned sale.
 *
 * @param array &$arguments Unused.
 * @param mixed $name Unused.
 * @param object &$object The sale being saved.
 */
function com_customer__check_return(&$arguments, $name, &$object) {
	global $_;

	if (!is_object($object) || !$_->config->com_customer->follow_up)
		return;
	// Add a check here for return->products == return->sale->products ???
	if ( $object->status == 'processed' && isset($object->sale->guid) &&
		($object->sale->followed_up || $object->sale->wh_followed_up) )
		com_customer__cancel_appointments($object->sale);
}

$_->hook->add_callback('com_sales_return->save', -10, 'com_customer__check_return');