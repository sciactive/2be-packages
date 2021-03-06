<?php
/**
 * Show completed checkout.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!gatekeeper()) {
	pines_redirect(pines_url('com_shop', 'checkout/login'));
	return;
}

if ($_->config->com_shop->catalog_mode)
	return;

// Load the sale.
$sale = com_sales_sale::factory((int) $_REQUEST['id']);
if (!isset($sale->guid) || !$_SESSION['user']->is($sale->customer)) {
	pines_notice('Invalid sale ID was given.');
	return;
}

$module = new module('com_shop', 'checkout/complete', 'content');
$module->entity = $sale;