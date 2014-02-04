<?php
/**
 * Provide a form to edit a manufacturer.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if (!empty($_REQUEST['id'])) {
	if ( !gatekeeper('com_sales/editmanufacturer') )
		punt_user(null, pines_url('com_sales', 'manufacturer/edit', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_sales/newmanufacturer') )
		punt_user(null, pines_url('com_sales', 'manufacturer/edit'));
}

$entity = com_sales_manufacturer::factory((int) $_REQUEST['id']);
$entity->print_form();