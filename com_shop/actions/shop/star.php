<?php
/**
 * Star a shop.
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

if ( !gatekeeper() )
	return;
$shop = com_shop_shop::factory((int) $_REQUEST['id']);
if (!isset($shop->guid))
	return;

$_->session('write');

while (true) {
	if ((array) $_SESSION['user']->com_shop_starred !== $_SESSION['user']->com_shop_starred) {
		$_SESSION['user']->com_shop_starred = array($shop);
		$_->page->override_doc(json_encode(array('starred' => true)));
		break;
	}

	$key = $shop->array_search($_SESSION['user']->com_shop_starred);
	if ($key === false) {
		$_SESSION['user']->com_shop_starred[] = $shop;
		$_->page->override_doc(json_encode(array('starred' => true)));
		break;
	} else {
		unset($_SESSION['user']->com_shop_starred[$key]);
		$_SESSION['user']->com_shop_starred = array_values($_SESSION['user']->com_shop_starred);
		$_->page->override_doc(json_encode(array('starred' => false)));
		break;
	}
	break;
}

if (!$_SESSION['user']->save()) {
	$_->page->override_doc('false');
}
