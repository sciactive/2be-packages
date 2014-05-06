<?php
/**
 * View the cart.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_shop->catalog_mode) {
	// Page title.
	$_->page->title_pre("Cart - ");

	$module = new module('com_shop', 'cart/view', 'content');
}