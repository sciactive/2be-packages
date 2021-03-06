<?php
/**
 * Provide a form to edit a return checklist.
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
	if ( !gatekeeper('com_sales/editreturnchecklist') )
		punt_user(null, pines_url('com_sales', 'returnchecklist/edit', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_sales/newreturnchecklist') )
		punt_user(null, pines_url('com_sales', 'returnchecklist/edit'));
}

$entity = com_sales_return_checklist::factory((int) $_REQUEST['id']);
$entity->print_form();