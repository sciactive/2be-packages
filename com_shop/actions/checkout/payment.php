<?php
/**
 * Payment step of checkout.
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
if (!$_->com_shop->build_sale())
	return;

// Load the steps module.
$_->com_shop->checkout_step('3');

// Load the review module if the pages are combined.
if ($_->config->com_shop->review_in_payment_page) {
	$module = new module('com_shop', 'checkout/review', 'content');
	$module->entity = $_SESSION['com_shop_sale'];
	$module->no_form = true;
}

$module = new module('com_shop', 'checkout/payment', 'content');
$module->payment_types = (array) $_->entity_manager->get_entities(
		array('class' => com_sales_payment_type, 'skip_ac' => true),
		array('&',
			'tag' => array('com_sales', 'payment_type'),
			'data' => array('shop', true)
		)
	);

// Show the extra review controls if the pages are combined.
$module->review_form = $_->config->com_shop->review_in_payment_page;

if (empty($_SESSION['com_shop_sale']->payments))
	$module->payment = (object) array();
else
	$module->payment = (object) $_SESSION['com_shop_sale']->payments[0];