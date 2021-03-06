<?php
/**
 * Provide a form to edit a return.
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
	if ( !gatekeeper('com_sales/editreturn') )
		punt_user(null, pines_url('com_sales', 'return/edit', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_sales/newreturn') )
		punt_user(null, pines_url('com_sales', 'return/edit'));
}

$entity = com_sales_return::factory((int) $_REQUEST['id']);
$entity->print_form();