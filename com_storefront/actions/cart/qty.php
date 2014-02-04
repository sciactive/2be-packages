<?php
/**
 * Adjust a product's quantity in the cart.
 *
 * @package Components\storefront
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

$_->page->override = true;
header('Content-Type: application/json');
$_->page->override_doc(json_encode($_->com_storefront->adjust_quantity((int) $_REQUEST['id'], (int) $_REQUEST['qty'])));