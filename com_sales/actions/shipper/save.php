<?php
/**
 * Save changes to a shipper.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( isset($_REQUEST['id']) ) {
	if ( !gatekeeper('com_sales/editshipper') )
		punt_user(null, pines_url('com_sales', 'shipper/list'));
	$shipper = com_sales_shipper::factory((int) $_REQUEST['id']);
	if (!isset($shipper->guid)) {
		pines_error('Requested shipper id is not accessible.');
		return;
	}
} else {
	if ( !gatekeeper('com_sales/newshipper') )
		punt_user(null, pines_url('com_sales', 'shipper/list'));
	$shipper = com_sales_shipper::factory();
}

$shipper->name = $_REQUEST['name'];
$shipper->email = $_REQUEST['email'];
$shipper->phone_work = preg_replace('/\D/', '', $_REQUEST['phone_work']);
$shipper->fax = preg_replace('/\D/', '', $_REQUEST['fax']);
$shipper->address_type = $_REQUEST['address_type'];
$shipper->address_1 = $_REQUEST['address_1'];
$shipper->address_2 = $_REQUEST['address_2'];
$shipper->city = $_REQUEST['city'];
$shipper->state = $_REQUEST['state'];
$shipper->zip = $_REQUEST['zip'];
$shipper->address_international = $_REQUEST['address_international'];
$shipper->account_number = $_REQUEST['account_number'];
$shipper->terms = $_REQUEST['terms'];
$shipper->comments = $_REQUEST['comments'];

// Shipment Tracking
$shipper->tracking = $_REQUEST['tracking'];
if ($shipper->tracking == 'custom')
	$shipper->tracking_url = $_REQUEST['tracking_url'];
else
	unset($shipper->tracking_url);

if (empty($shipper->name)) {
	$shipper->print_form();
	pines_notice('Please specify a name.');
	return;
}
$test = $_->nymph->getEntity(array('class' => com_sales_shipper, 'skip_ac' => true), array('&', 'tag' => array('com_sales', 'shipper'), 'data' => array('name', $shipper->name)));
if (isset($test) && $test->guid != $_REQUEST['id']) {
	$shipper->print_form();
	pines_notice('There is already a shipper with that name. Please choose a different name.');
	return;
}

if ($_->config->com_sales->global_shippers)
	$shipper->ac->other = 1;

if ($shipper->save()) {
	pines_notice('Saved shipper ['.$shipper->name.']');
} else {
	pines_error('Error saving shipper. Do you have permission?');
}

pines_redirect(pines_url('com_sales', 'shipper/list'));