<?php
/**
 * Edit configuration settings.
 *
 * @package Components\configure
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ($_REQUEST['peruser']) {
	if ( !gatekeeper('com_configure/editperuser'))
		punt_user(null, pines_url('com_configure', 'edit', $_GET));
} else {
	if ( !gatekeeper('com_configure/edit') )
		punt_user(null, pines_url('com_configure', 'edit', $_GET));
}

if (!array_key_exists($_REQUEST['component'], $_->configurator->component_files)) {
	pines_error('Given component either does not exist, or has no configuration file!');
	return;
}

$component = configurator_component::factory($_REQUEST['component']);
if ($_REQUEST['peruser']) {
	if ($_REQUEST['type'] == 'group') {
		$user = group::factory((int) $_REQUEST['id']);
	} else {
		$user = user::factory((int) $_REQUEST['id']);
	}
	if (!isset($user->guid)) {
		pines_error('Requested user/group id is not accessible.');
		return;
	}
	$component->set_per_user($user);
} elseif ($_REQUEST['percondition']) {
	$user = com_configure_condition::factory((int) $_REQUEST['id']);
	if (!isset($user->guid)) {
		pines_error('Requested condition id is not accessible.');
		return;
	}
	$component->set_per_user($user);
}
$component->print_form();