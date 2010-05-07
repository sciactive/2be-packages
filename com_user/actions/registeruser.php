<?php
/**
 * Save changes to a new user registration.
 *
 * @package Pines
 * @subpackage com_user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if (!$pines->config->com_user->allow_registration)
	punt_user('User registration not allowed.');

if (empty($_SESSION['com_user__tmpusername']) || empty($_SESSION['com_user__tmpusername'])) {
	pines_notice('Username and password could not be recalled.');
	return;
}

$user = user::factory();
$user->grant('com_user/login');

$user->username = $_SESSION['com_user__tmpusername'];
$user->password($_SESSION['com_user__tmppassword']);
$user->name = $_REQUEST['name'];
$user->email = $_REQUEST['email'];
$user->phone = preg_replace('/\D/', '', $_REQUEST['phone']);
$user->fax = preg_replace('/\D/', '', $_REQUEST['fax']);
$user->timezone = $_REQUEST['timezone'];

// Location
$user->address_type = $_REQUEST['address_type'];
$user->address_1 = $_REQUEST['address_1'];
$user->address_2 = $_REQUEST['address_2'];
$user->city = $_REQUEST['city'];
$user->state = $_REQUEST['state'];
$user->zip = $_REQUEST['zip'];
$user->address_international = $_REQUEST['address_international'];

if ($pines->config->com_user->max_username_length > 0 && strlen($user->username) > $pines->config->com_user->max_username_length) {
	$user->register();
	pines_notice("Usernames must not exceed {$pines->config->com_user->max_username_length} characters.");
	return;
}
if (empty($user->password) && !$pines->config->com_user->empty_pw) {
	$user->register();
	pines_notice('Please specify a password.');
	return;
}

if ($pines->config->com_user->confirm_email) {
	// The user will be enabled after confirming their e-mail address.
	$user->enabled = false;
	$user->secret = uniqid('', true);
} else {
	$user->enabled = true;
}

if ($user->save()) {
	pines_log('Registered user ['.$user->username.']');
	unset($_SESSION['com_user__tmpusername']);
	unset($_SESSION['com_user__tmppassword']);
	if ($pines->config->com_user->confirm_email) {
		// Send the verification e-mail.
		$link = '<a href="'.htmlentities(pines_url('com_user', 'verifyuser', array('id' => $user->guid, 'secret' => $user->secret), true)).'">'.htmlentities(pines_url('com_user', 'verifyuser', array('id' => $user->guid, 'secret' => $user->secret), true)).'</a>';
		$search = array(
			'{site_title}',
			'{site_address}',
			'{link}',
			'{username}',
			'{name}',
			'{email}',
			'{phone}',
			'{fax}',
			'{timezone}',
			'{address}'
		);
		$replace = array(
			$pines->config->option_title,
			$pines->config->full_location,
			$link,
			$user->username,
			$user->name,
			$user->email,
			$user->phone,
			$user->fax,
			$user->timezone,
			$user->address_type == 'US' ? "{$user->address_1} {$user->address_2}\n{$user->city}, {$user->state} {$user->zip}" : $user->address_international
		);
		$subject = str_replace($search, $replace, $pines->config->com_user->email_subject);
		$content = str_replace($search, $replace, $pines->config->com_user->email_content);
		$mail = com_mailer_mail::factory($pines->config->com_user->email_from_address, $user->email, $subject, $content);
		if ($mail->send()) {
			$note = new module('com_user', 'note_verify_email', 'content');
			$note->entity = $user;
		} else {
			pines_error('Couldn\'t send registration email.');
			return;
		}
	} else {
		$pines->user_manager->login($user);
		$note = new module('com_user', 'note_welcome', 'content');
	}
} else {
	pines_error('Error registering user.');
}

?>