<?php
/**
 * Provide a form to edit a product.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!empty($_REQUEST['id'])) {
	if ( !gatekeeper('com_sales/editproduct') )
		punt_user(null, pines_url('com_sales', 'product/edit', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_sales/newproduct') )
		punt_user(null, pines_url('com_sales', 'product/edit'));
}

if ($_->config->com_sales->com_shop && !isset($_SESSION['shop'])) {
	pines_notice('You must have a shop to add products.');
	pines_redirect(pines_url('com_shop', 'shop/edit'));
}

$entity = com_sales_product::factory((int) $_REQUEST['id']);
$entity->print_form();