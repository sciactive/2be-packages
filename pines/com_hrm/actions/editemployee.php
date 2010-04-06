<?php
/**
 * Provide a form to edit an employee.
 *
 * @package Pines
 * @subpackage com_hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if (isset($_REQUEST['id'])) {
	if ( !gatekeeper('com_hrm/editemployee') )
		punt_user('You don\'t have necessary permission.', pines_url('com_hrm', 'editemployee', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_hrm/newemployee') )
		punt_user('You don\'t have necessary permission.', pines_url('com_hrm', 'editemployee'));
}

$entity = com_hrm_employee::factory((int) $_REQUEST['id']);
$entity->print_form();

?>