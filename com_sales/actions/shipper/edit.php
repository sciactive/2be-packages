<?php
/**
 * Provide a form to edit a shipper.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

if (!empty($_REQUEST['id'])) {
	if ( !gatekeeper('com_sales/editshipper') )
		punt_user(null, pines_url('com_sales', 'shipper/edit', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_sales/newshipper') )
		punt_user(null, pines_url('com_sales', 'shipper/edit'));
}

$entity = com_sales_shipper::factory((int) $_REQUEST['id']);
$entity->print_form();