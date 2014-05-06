<?php
/**
 * List shops.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_shop/listshops') )
	punt_user(null, pines_url('com_shop', 'shop/list'));

$_->com_shop->list_shops();