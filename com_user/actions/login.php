<?php
/**
 * Log a user into the system.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (empty($_REQUEST['username'])) {
	$_->user_manager->print_login('content', $_REQUEST['url']);
	return;
}

if ($_->config->com_user->allow_registration && $_REQUEST['existing'] != 'ON') {
	if (empty($_REQUEST['password']) && !$_->config->com_user->pw_empty) {
		pines_notice('Password is a required field.');
		$_->user_manager->print_login('content', $_REQUEST['url']);
		return;
	}
	$un_check = $_->user_manager->check_username($_REQUEST['username']);
	if (!$un_check['result']) {
		$_->user_manager->print_login('content', $_REQUEST['url']);
		pines_notice($un_check['message']);
		return;
	}
	$user = user::factory();
	pines_session('write');
	$_SESSION['com_user__tmpusername'] = $_REQUEST['username'];
	$_SESSION['com_user__tmppassword'] = $_REQUEST['password'];
	$_SESSION['com_user__tmpreferral_code'] = $_REQUEST['referral_code'];
	pines_session('close');
	if ($_->config->com_user->one_step_registration) {
		$_->action('com_user', 'registeruser');
	} else {
		$reg_module = $user->print_register();
		if ( !empty($_REQUEST['url']) )
			$reg_module->url = $_REQUEST['url'];
	}
	return;
}

$username = $_REQUEST['username'];
if ($_->config->com_user->email_usernames && strpos($username, '@') === false && !empty($_->config->com_user->default_domain))
	$username .= '@'.$_->config->com_user->default_domain;

if (gatekeeper() && $username == $_SESSION['user']->username) {
	pines_notice('You are already logged in.');
	pines_redirect(pines_url());
	return;
}
// Check that a challenge block was created within 10 minutes.
if (($_->config->com_user->sawasc && $_->config->com_user->pw_method != 'salt') && (!isset($_SESSION['sawasc']['ServerCB']) || $_SESSION['sawasc']['timestamp'] < time() - 600)) {
	pines_notice('Your login request session has expired, please try again.');
	$_->user_manager->print_login();
	return;
}
$user = user::factory($username);
if (!isset($user->guid)) {
	pines_notice('Incorrect login/password.');
	$_->user_manager->print_login();
	return;
}
if ($_->config->com_user->sawasc && $_->config->com_user->pw_method != 'salt') {
	pines_session('write');
	if (!$user->check_sawasc($_REQUEST['ClientHash'], $_SESSION['sawasc']['ServerCB'], $_SESSION['sawasc']['algo'])) {
		unset($_SESSION['sawasc']);
		pines_session('close');
		pines_notice('Incorrect login/password.');
		$_->user_manager->print_login();
		return;
	}
	unset($_SESSION['sawasc']);
	pines_session('close');
} else {
	if (!$user->check_password($_REQUEST['password'])) {
		pines_notice('Incorrect login/password.');
		$_->user_manager->print_login();
		return;
	}
}

// Authentication was successful, attempt to login.
if (!$_->user_manager->login($user)) {
	pines_notice('Incorrect login/password.');
	$_->user_manager->print_login();
	return;
}

// Login was successful.
if (!empty($_REQUEST['url'])) {
	pines_redirect(urldecode($_REQUEST['url']));
	return;
}
// Load the default component.
pines_redirect(pines_url());
