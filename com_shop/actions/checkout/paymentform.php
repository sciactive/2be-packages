<?php
/**
 * Provide a form for a payment process type to collect information.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

$_->page->override = true;

if (!gatekeeper())
	return;

if ($_->config->com_shop->catalog_mode)
	return;

// Load the sale.
if (!$_->com_shop->build_sale())
	return;

$_->com_sales->call_payment_process(array(
	'action' => 'request_cust',
	'name' => $_REQUEST['name'],
	'ticket' => $_SESSION['com_shop_sale']
), $module);

if (isset($module))
	$_->page->ajax($module->render(), 'text/html');