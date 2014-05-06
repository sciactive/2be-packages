<?php
/**
 * Review step of checkout.
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
$_->com_shop->checkout_step('4');

$module = new module('com_shop', 'checkout/review', 'content');
$module->entity = $_SESSION['com_shop_sale'];