<?php
/**
 * Shipping step of checkout.
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

if (
		$_->config->com_shop->skip_shipping &&
		$_REQUEST['noskip'] != 'true' &&
		!(
			empty($_SESSION['user']->name) ||
			(
				(
					empty($_SESSION['user']->address_1) ||
					empty($_SESSION['user']->city) ||
					empty($_SESSION['user']->state) ||
					empty($_SESSION['user']->zip)
				)
				&&
				empty($_SESSION['user']->address_international)
			)
		)
	) {
	pines_session('write');
	$_SESSION['com_shop_sale']->shipping_use_customer = false;
	$_SESSION['com_shop_sale']->shipping_address = (object) array(
		'name' => $_SESSION['user']->name,
		'address_type' => $_SESSION['user']->address_type == 'international' ? 'international' : 'us',
		'address_1' => $_SESSION['user']->address_1,
		'address_2' => $_SESSION['user']->address_2,
		'city' => $_SESSION['user']->city,
		'state' => $_SESSION['user']->state,
		'zip' => $_SESSION['user']->zip,
		'address_international' => $_SESSION['user']->address_international
	);
	pines_session('close');
	pines_redirect(pines_url('com_shop', 'checkout/payment'));
	return;
}

// Load the steps module.
$_->com_shop->checkout_step('2');

$module = new module('com_shop', 'checkout/shipping', 'content');
$module->user_address = (object) array(
	'name' => $_SESSION['user']->name,
	'address_type' => $_SESSION['user']->address_type == 'international' ? 'international' : 'us',
	'address_1' => $_SESSION['user']->address_1,
	'address_2' => $_SESSION['user']->address_2,
	'city' => $_SESSION['user']->city,
	'state' => $_SESSION['user']->state,
	'zip' => $_SESSION['user']->zip,
	'address_international' => $_SESSION['user']->address_international
);
$module->address = (object) $_SESSION['com_shop_sale']->shipping_address;