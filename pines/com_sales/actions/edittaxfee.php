<?php
/**
 * Provide a form to edit a tax/fee.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if (isset($_REQUEST['id'])) {
	if ( !gatekeeper('com_sales/edittaxfee') )
		punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'edittaxfee', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_sales/newtaxfee') )
		punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'edittaxfee'));
}

$entity = com_sales_tax_fee::factory((int) $_REQUEST['id']);
$entity->print_form();

?>