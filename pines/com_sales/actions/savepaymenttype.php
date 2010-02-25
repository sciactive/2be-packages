<?php
/**
 * Save changes to a payment type.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( isset($_REQUEST['id']) ) {
	if ( !gatekeeper('com_sales/editpaymenttype') )
		punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'listpaymenttypes', null, false));
	$payment_type = com_sales_payment_type::factory((int) $_REQUEST['id']);
	if (is_null($payment_type->guid)) {
		display_error('Requested payment type id is not accessible');
		return;
	}
} else {
	if ( !gatekeeper('com_sales/newpaymenttype') )
		punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'listpaymenttypes', null, false));
	$payment_type = com_sales_payment_type::factory();
}

$payment_type->name = $_REQUEST['name'];
$payment_type->enabled = ($_REQUEST['enabled'] == 'ON');
$payment_type->kick_drawer = ($_REQUEST['kick_drawer'] == 'ON');
$payment_type->change_type = ($_REQUEST['change_type'] == 'ON');
$payment_type->minimum = floatval($_REQUEST['minimum']);
$payment_type->maximum = floatval($_REQUEST['maximum']);
$payment_type->processing_type = $_REQUEST['processing_type'];

if (empty($payment_type->name)) {
	$payment_type->print_form();
	display_notice('Please specify a name.');
	return;
}
$test = $pines->entity_manager->get_entity(array('data' => array('name' => $payment_type->name), 'tags' => array('com_sales', 'payment_type'), 'class' => com_sales_payment_type));
if (isset($test) && $test->guid != $_REQUEST['id']) {
	$payment_type->print_form();
	display_notice('There is already a payment type with that name. Please choose a different name.');
	return;
}
if (empty($payment_type->processing_type)) {
	$payment_type->print_form();
	display_notice('Please specify a processing type.');
	return;
}
if (empty($payment_type->minimum))
	$payment_type->minimum = 0;
if (empty($payment_type->maximum))
	$payment_type->maximum = null;

if ($pines->config->com_sales->global_payment_types)
	$payment_type->ac->other = 1;

if ($payment_type->change_type) {
	$change_type = $pines->entity_manager->get_entity(array('data' => array('change_type' => true), 'tags' => array('com_sales', 'payment_type'), 'class' => com_sales_payment_type));
	if (isset($change_type) && $change_type->guid != $_REQUEST['id']) {
		$change_type->change_type = false;
		if ($change_type->save()) {
			display_notice("Change type changed from [{$change_type->name}] to [{$payment_type->name}].");
		} else {
			$module = $pines->com_sales->print_payment_type_form('com_sales', 'savepaymenttype');
			$module->entity = $payment_type;
			display_error("There was an error while changing change type from {$change_type->name}. Do you have permission to edit the current change type?");
			return;
		}
	}
}

if ($payment_type->save()) {
	display_notice('Saved payment type ['.$payment_type->name.']');
} else {
	display_error('Error saving payment type. Do you have permission?');
}

$pines->com_sales->list_payment_types();
?>