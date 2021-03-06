<?php
/**
 * Adjust a product's quantity in the cart.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

$_->page->ajax(json_encode($_->com_shop->adjust_quantity((int) $_REQUEST['id'], (int) $_REQUEST['qty'])));
