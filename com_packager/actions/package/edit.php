<?php
/**
 * Provide a form to edit a package.
 *
 * @package Components\packager
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if (!empty($_REQUEST['id'])) {
	if ( !gatekeeper('com_packager/editpackage') )
		punt_user(null, pines_url('com_packager', 'package/edit', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_packager/newpackage') )
		punt_user(null, pines_url('com_packager', 'package/edit'));
}

$entity = com_packager_package::factory((int) $_REQUEST['id']);
$entity->print_form();