<?php
/**
 * Log a user into the system.
 *
 * @package Components\su
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_su/switch') )
	punt_user(null, pines_url());

if ( empty($_REQUEST['username']) && empty($_REQUEST['pin']) ) {
	$_->user_manager->print_login();
	return;
}
if ( gatekeeper() && $_REQUEST['username'] == $_SESSION['user']->username ) {
	pines_notice('Already logged in!');
	return;
}
if (!empty($_REQUEST['pin']) && $_->config->com_su->allow_pins) {
	$users = $_->user_manager->get_users();
	foreach ($users as $cur_user) {
		if (empty($cur_user->pin))
			continue;
		if ($_REQUEST['pin'] == $cur_user->pin) {
			$user = $cur_user;
			pines_log("Used PIN to access {$user->username}.", 'notice');
			break;
		}
	}
} else {
	$user = user::factory($_REQUEST['username']);
	if (!gatekeeper('com_su/nopassword') && !$user->check_password($_REQUEST['password']))
		unset($user);
}
if ( isset($user, $user->guid) ) {
	pines_log("Switching user from {$_SESSION['user']->username} to {$user->username}.", 'notice');
	pines_notice("Switching user from {$_SESSION['user']->username} to {$user->username}.");
	if ($_->user_manager->login($user)) {
		pines_redirect(pines_url());
	} else {
		pines_error('Could not switch users.');
		// Load the default component.
		pines_action();
	}
} else {
	pines_notice('Username and password not correct!');
	$_->user_manager->print_login();
}
