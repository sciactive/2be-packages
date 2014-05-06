<?php
/**
 * Verify the inventory that is listed in the shop.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/editproduct') )
	punt_user(null, pines_url('com_shop', 'verifystock'));

$_->com_shop->verify_stock();