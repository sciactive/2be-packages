<?php
/**
 * Disable a component.
 *
 * @package Components\configure
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_configure/edit') )
	punt_user(null, pines_url('com_configure', 'edit', $_GET));

if ($_->configurator->enable_component($_REQUEST['component'])) {
	pines_notice("Component {$_REQUEST['component']} successfully enabled.");
	pines_redirect(pines_url('com_configure', 'list'));
	exit;
} else {
	pines_error('Couldn\'t enable component '.$_REQUEST['component'].'.');
}

pines_redirect(pines_url('com_configure', 'list'));