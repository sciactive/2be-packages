<?php
/**
 * Save changes to a vendor.
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
	if ( !gatekeeper('com_sales/editvendor') )
		punt_user(null, pines_url('com_sales', 'vendor/list'));
	$vendor = com_sales_vendor::factory((int) $_REQUEST['id']);
	if (!isset($vendor->guid)) {
		pines_error('Requested vendor id is not accessible.');
		return;
	}
} else {
	if ( !gatekeeper('com_sales/newvendor') )
		punt_user(null, pines_url('com_sales', 'vendor/list'));
	$vendor = com_sales_vendor::factory();
}

$vendor->name = $_REQUEST['name'];
$vendor->email = $_REQUEST['email'];
$vendor->phone_work = preg_replace('/\D/', '', $_REQUEST['phone_work']);
$vendor->fax = preg_replace('/\D/', '', $_REQUEST['fax']);
$vendor->address_type = $_REQUEST['address_type'];
$vendor->address_1 = $_REQUEST['address_1'];
$vendor->address_2 = $_REQUEST['address_2'];
$vendor->city = $_REQUEST['city'];
$vendor->state = $_REQUEST['state'];
$vendor->zip = $_REQUEST['zip'];
$vendor->address_international = $_REQUEST['address_international'];
$vendor->account_number = $_REQUEST['account_number'];
$vendor->client_username = $_REQUEST['client_username'];
$vendor->client_password = $_REQUEST['client_password'];
$vendor->client_rep_name = $_REQUEST['client_rep_name'];
$vendor->client_email = $_REQUEST['client_email'];
$vendor->client_web_address = $_REQUEST['client_web_address'];
$vendor->online_web_address = $_REQUEST['online_web_address'];
$vendor->online_customer_id = $_REQUEST['online_customer_id'];
$vendor->online_username = $_REQUEST['online_username'];
$vendor->online_password = $_REQUEST['online_password'];
$vendor->terms = $_REQUEST['terms'];
$vendor->comments = $_REQUEST['comments'];

if ($_REQUEST['remove_logo'] == 'ON' && isset($vendor->logo))
	unset($vendor->logo);

// Logo image.
if (!empty($_REQUEST['logo']) && $_->uploader->check($_REQUEST['logo']))
	$vendor->logo = $_REQUEST['logo'];

if (empty($vendor->name)) {
	$vendor->print_form();
	pines_notice('Please specify a name.');
	return;
}
$test = $_->nymph->getEntity(array('class' => com_sales_vendor, 'skip_ac' => true), array('&', 'tag' => array('com_sales', 'vendor'), 'data' => array('name', $vendor->name)));
if (isset($test) && $test->guid != $_REQUEST['id']) {
	$vendor->print_form();
	pines_notice('There is already a vendor with that name. Please choose a different name.');
	return;
}

if ($_->config->com_sales->global_vendors)
	$vendor->ac->other = 1;

if ($vendor->save()) {
	pines_notice('Saved vendor ['.$vendor->name.']');
} else {
	pines_error('Error saving vendor. Do you have permission?');
}

pines_redirect(pines_url('com_sales', 'vendor/list'));