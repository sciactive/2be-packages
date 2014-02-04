<?php
/**
 * Provide a form to edit a module.
 *
 * @package Components\modules
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if (!empty($_REQUEST['id'])) {
	if ( !gatekeeper('com_modules/editmodule') )
		punt_user(null, pines_url('com_modules', 'module/edit', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_modules/newmodule') )
		punt_user(null, pines_url('com_modules', 'module/edit'));
}

$entity = com_modules_module::factory((int) $_REQUEST['id']);
$entity->print_form();