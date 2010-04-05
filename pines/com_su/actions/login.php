<?php
/**
 * Log a user into the system.
 *
 * @package Pines
 * @subpackage com_su
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( empty($_REQUEST['username']) ) {
$pines->user_manager->print_login();
	return;
}
if ( gatekeeper() && $_REQUEST['username'] == $_SESSION['user']->username ) {
	pines_notice('Already logged in!');
	return;
}
$user = user::factory($_REQUEST['username']);
if ( isset($user->guid) && (gatekeeper('com_su/nopassword') || $user->check_password($_REQUEST['password'])) ) {
	pines_log("Switching user from {$_SESSION['user']->username} to {$user->username}.", 'notice');
	if ($pines->user_manager->login($user)) {
		header('HTTP/1.1 303 See Other', true, 303);
		header('Location: '.pines_url(null, null, array(), false));
	} else {
		pines_error('Could not switch users.');
		// Load the default component.
		action($pines->config->default_component, 'default');
	}
} else {
	pines_notice('Username and password not correct!');
	$pines->user_manager->print_login();
}

?>