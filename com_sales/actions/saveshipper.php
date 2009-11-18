<?php
/**
 * Save changes to a shipper.
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
	if ( !gatekeeper('com_sales/editshipper') ) {
		$config->user_manager->punt_user("You don't have necessary permission.", pines_url('com_sales', 'listshippers', null, false));
		return;
	}
	$shipper = $config->run_sales->get_shipper($_REQUEST['id']);
	if (is_null($shipper)) {
		display_error('Requested shipper id is not accessible');
		return;
	}
} else {
	if ( !gatekeeper('com_sales/newshipper') ) {
		$config->user_manager->punt_user("You don't have necessary permission.", pines_url('com_sales', 'listshippers', null, false));
		return;
	}
	$shipper = new entity;
	$shipper->add_tag('com_sales', 'shipper');
}

$shipper->name = $_REQUEST['name'];
$shipper->email = $_REQUEST['email'];
$shipper->address_1 = $_REQUEST['address_1'];
$shipper->address_2 = $_REQUEST['address_2'];
$shipper->city = $_REQUEST['city'];
$shipper->state = $_REQUEST['state'];
$shipper->zip = $_REQUEST['zip'];
$shipper->phone_work = $_REQUEST['phone_work'];
$shipper->fax = $_REQUEST['fax'];
$shipper->account_number = $_REQUEST['account_number'];
$shipper->terms = $_REQUEST['terms'];
$shipper->comments = $_REQUEST['comments'];

if (empty($shipper->name)) {
	$module = $config->run_sales->print_shipper_form('com_sales', 'saveshipper');
	$module->entity = $shipper;
	display_error('Please specify a name.');
	return;
}
$test = $config->entity_manager->get_entities_by_data(array('name' => $shipper->name), array('com_sales', 'shipper'));
if (!empty($test) && $test[0]->guid != $_REQUEST['id']) {
	$module = $config->run_sales->print_shipper_form('com_sales', 'saveshipper');
	$module->entity = $shipper;
	display_error('There is already a shipper with that name. Please choose a different name.');
	return;
}

if ($config->com_sales->global_shippers) {
	$shipper->ac = (object) array('other' => 1);
}

if ($shipper->save()) {
	display_notice('Saved shipper ['.$shipper->name.']');
} else {
	display_error('Error saving shipper. Do you have permission?');
}

$config->run_sales->list_shippers();
?>