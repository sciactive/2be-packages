<?php
/**
 * Provide a form for a payment process type to collect information.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ($_REQUEST['type'] == 'return') {
	if ( isset($_REQUEST['id']) ) {
		if ( !gatekeeper('com_sales/editreturn') )
			punt_user(null, pines_url('com_sales', 'sale/list'));
		$ticket = com_sales_return::factory((int) $_REQUEST['id']);
	} else {
		if ( !gatekeeper('com_sales/newreturn') && !gatekeeper('com_sales/newreturnwsale') )
			punt_user(null, pines_url('com_sales', 'sale/list'));
		$ticket = com_sales_return::factory();
		if ( isset($_REQUEST['sale_id']) ) {
			$sale = com_sales_sale::factory((int) $_REQUEST['sale_id']);
			if (isset($sale->guid))
				$ticket->attach_sale($sale);
		}
	}
} else {
	if ( isset($_REQUEST['id']) ) {
		if ( !gatekeeper('com_sales/editsale') )
			punt_user(null, pines_url('com_sales', 'sale/list'));
		$ticket = com_sales_sale::factory((int) $_REQUEST['id']);
	} else {
		if ( !gatekeeper('com_sales/newsale') )
			punt_user(null, pines_url('com_sales', 'sale/list'));
		$ticket = com_sales_sale::factory();
	}
}

if ($_->config->com_sales->com_customer && $ticket->status != 'invoiced' && $ticket->status != 'paid' && $ticket->status != 'processed' && $ticket->status != 'voided') {
	$ticket->customer = null;
	if (preg_match('/^\d+/', $_REQUEST['customer'])) {
		$ticket->customer = com_customer_customer::factory((int) $_REQUEST['customer']);
		if (!isset($ticket->customer->guid))
			$ticket->customer = null;
	}
}

$_->page->override = true;
$_->com_sales->call_payment_process(array(
	'action' => 'request',
	'name' => $_REQUEST['name'],
	'ticket' => $ticket
), $module);

if (isset($module))
	$_->page->override_doc($module->render());