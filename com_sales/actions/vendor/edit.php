<?php
/**
 * Provide a form to edit a vendor.
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
	if ( !gatekeeper('com_sales/editvendor') )
		punt_user(null, pines_url('com_sales', 'vendor/edit', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_sales/newvendor') )
		punt_user(null, pines_url('com_sales', 'vendor/edit'));
}

$entity = com_sales_vendor::factory((int) $_REQUEST['id']);
$entity->print_form();