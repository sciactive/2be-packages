<?php
/**
 * Show completed checkout.
 *
 * @package Components\storefront
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

if (!gatekeeper()) {
	pines_redirect(pines_url('com_storefront', 'checkout/login'));
	return;
}

if ($pines->config->com_storefront->catalog_mode)
	return;

// Load the sale.
$sale = com_sales_sale::factory((int) $_REQUEST['id']);
if (!isset($sale->guid) || !$_SESSION['user']->is($sale->customer)) {
	pines_notice('Invalid sale ID was given.');
	return;
}

$module = new module('com_storefront', 'checkout/complete', 'content');
$module->entity = $sale;