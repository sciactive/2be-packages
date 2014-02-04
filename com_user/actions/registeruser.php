<?php
/**
 * Save changes to a new user registration.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
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
if ($pines->config->com_user->referral_codes)
	$user->referral_code = $_SESSION['com_user__tmpreferral_code'];
if (in_array('name', $pines->config->com_user->reg_fields)) {
	$user->name_first = $_REQUEST['name_first'];
	$user->name_middle = $_REQUEST['name_middle'];
	$user->name_last = $_REQUEST['name_last'];
	$user->name = $user->name_first.(!empty($user->name_middle) ? ' '.$user->name_middle : '').(!empty($user->name_last) ? ' '.$user->name_last : '');
}
if (!$pines->config->com_user->email_usernames && in_array('email', $pines->config->com_user->reg_fields))
	$user->email = $_REQUEST['email'];
if (in_array('phone', $pines->config->com_user->reg_fields))
	$user->phone = preg_replace('/\D/', '', $_REQUEST['phone']);
if (in_array('fax', $pines->config->com_user->reg_fields))
	$user->fax = preg_replace('/\D/', '', $_REQUEST['fax']);
if (in_array('timezone', $pines->config->com_user->reg_fields))
	$user->timezone = $_REQUEST['timezone'];

// Location
if (in_array('address', $pines->config->com_user->reg_fields)) {
	$user->address_type = $_REQUEST['address_type'];
	$user->address_1 = $_REQUEST['address_1'];
	$user->address_2 = $_REQUEST['address_2'];
	$user->city = $_REQUEST['city'];
	$user->state = $_REQUEST['state'];
	$user->zip = $_REQUEST['zip'];
	$user->address_international = $_REQUEST['address_international'];
}

$un_check = $pines->user_manager->check_username($user->username, $user->guid);
if (!$un_check['result']) {
	$user->print_register();
	pines_notice($un_check['message']);
	return;
}
if (!$pines->config->com_user->email_usernames && in_array('email', $pines->config->com_user->reg_fields)) {
	$test = $pines->entity_manager->get_entity(
			array('class' => user, 'skip_ac' => true),
			array('&',
				'tag' => array('com_user', 'user'),
				'match' => array('email', '/^'.preg_quote($user->email, '/').'$/i')
			)
		);
	if (isset($test) && !$user->is($test)) {
		$user->print_register();
		pines_notice('There is already a user with that email address. Please use a different email.');
		return;
	}
}
if (empty($user->password) && !$pines->config->com_user->pw_empty) {
	$user->print_register();
	pines_notice('Please specify a password.');
	return;
}

$user->group = $pines->entity_manager->get_entity(array('class' => group), array('&', 'tag' => array('com_user', 'group'), 'data' => array('default_primary', true)));
if (!isset($user->group->guid))
	unset($user->group);
if ($pines->config->com_user->confirm_email && $pines->config->com_user->unconfirmed_access)
	$user->groups = (array) $pines->entity_manager->get_entities(array('class' => group, 'skip_ac' => true), array('&', 'tag' => array('com_user', 'group'), 'data' => array('unconfirmed_secondary', true)));
else
	$user->groups = (array) $pines->entity_manager->get_entities(array('class' => group, 'skip_ac' => true), array('&', 'tag' => array('com_user', 'group'), 'data' => array('default_secondary', true)));

if ($pines->config->com_user->confirm_email) {
	// The user will be enabled after confirming their e-mail address.
	if (!$pines->config->com_user->unconfirmed_access)
		$user->disable();
	$user->secret = uniqid('', true);
} else
	$user->enable();

// If create_admin is true and there are no other users, grant "system/all".
if ($pines->config->com_user->create_admin) {
	$other_users = $pines->entity_manager->get_entities(array('class' => user, 'skip_ac' => true, 'limit' => 1), array('&', 'tag' => array('com_user', 'user')));
	// Make sure it's not just null, cause that means an error.
	if ($other_users === array()) {
		$user->grant('system/all');
		$user->enable();
		pines_notice("Welcome to {$pines->config->system_name}. Since this is the first user account, your account has been granted all abilities.");
	}
}

if ($user->save()) {
	pines_log('Registered user ['.$user->username.']');
	// Send the new user registered email.
	$macros = array(
		'user_username' => h($user->username),
		'user_name' => h($user->name),
		'user_first_name' => h($user->name_first),
		'user_last_name' => h($user->name_last),
		'user_email' => h($user->email),
		'user_phone' => h(format_phone($user->phone)),
		'user_fax' => h(format_phone($user->fax)),
		'user_timezone' => h($user->timezone),
		'user_address' => $user->address_type == 'us' ? h("{$user->address_1} {$user->address_2}").'<br />'.h("{$user->city}, {$user->state} {$user->zip}") : '<pre>'.h($user->address_international).'</pre>'
	);
	$pines->com_mailer->send_mail('com_user/user_registered', $macros);
	pines_session('write');
	unset($_SESSION['com_user__tmpusername']);
	unset($_SESSION['com_user__tmppassword']);
	unset($_SESSION['com_user__tmpreferral_code']);
	pines_session('close');
	if ($pines->config->com_user->confirm_email) {
		// Send the verification email.
		if ($user->send_email_verification($_REQUEST['url'])) {
			$note = new module('com_user', 'note_verify_email', 'content');
			$note->entity = $user;
			if ($pines->config->com_user->unconfirmed_access) {
				$pines->user_manager->login($user);
				if ( !empty($_REQUEST['url']) ) {
					pines_redirect(urldecode($_REQUEST['url']));
					return;
				}
			}
		} else {
			pines_error('Couldn\'t send registration email.');
			return;
		}
	} else {
		$pines->user_manager->login($user);
		$pines->load_system_config();
		$note = new module('com_user', 'note_welcome', 'content');
		if ( !empty($_REQUEST['url']) ) {
			pines_redirect(urldecode($_REQUEST['url']));
			return;
		}
	}
} else {
	pines_error('Error registering user.');
}