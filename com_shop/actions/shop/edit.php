<?php
/**
 * Provide a form to edit a shop.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!empty($_REQUEST['id'])) {
	if ( !gatekeeper('com_shop/editshop') )
		punt_user(null, pines_url('com_shop', 'shop/edit', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_shop/newshop') )
		punt_user(null, pines_url('com_shop', 'shop/edit'));
}

$entity = com_shop_shop::factory((int) $_REQUEST['id']);

if (!gatekeeper('com_shop/manageshops') && !$_SESSION['user']->is($entity->user))
	punt_user(null, pines_url('com_shop', 'shop/list'));

$entity->print_form();