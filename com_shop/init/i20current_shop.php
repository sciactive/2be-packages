<?php
/**
 * Check for the current shop.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!gatekeeper('com_shop/newshop') && !gatekeeper('com_shop/editshop'))
	return;

$module = new module('com_shop', 'shop/selected', 'shop_thumbnail');
unset($module);

if (isset($_SESSION['shop']))
	return;

$shop = $_->nymph->getEntity(
		array('class' => com_shop_shop),
		array('&',
			'tag' => array('com_shop', 'shop'),
			'ref' => array('user', $_SESSION['user'])
		)
	);

if (isset($shop)) {
	$_->session('write');
	$_SESSION['shop'] = $shop;
	$_->session('close');
}
unset($shop);
