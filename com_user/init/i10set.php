<?php
/**
 * Set the system user manager, and add dependency checker.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * The user manager.
 * @global com_user $_->user_manager
 */
$_->user_manager = 'com_user';


$check_com_user__user = function($value, $help = false){
	global $_, $check_com_user__user;
	if ($help) {
		$return = array();
		$return['cname'] = 'User Checker';
		$return['description'] = <<<'EOF'
Check various aspects of the current user.
EOF;
		$return['syntax'] = <<<'EOF'
The user checker can check the following values:

- verified - The user's email has been verified.
- primary - The user has an enabled primary group.
- secondary - The user has at least one enabled secondary group.
- mailing - The user is subscribed to the mailing list.
- email - The user has provided their email address.
- phone - The user has provided their phone number.
- address - The user has provided their mailing address.
EOF;
		$return['examples'] = <<<'EOF'
verified
:	Check that the current user is verified.

phone&mailing
:	Check that the user has a phone number and is subscribed to the mailing list.
EOF;
		$return['simple_parse'] = true;
		return $return;
	}
	if ($value == '' || !isset($_SESSION['user']))
		return false;
	if (
			strpos($value, '&') !== false ||
			strpos($value, '|') !== false ||
			strpos($value, '!') !== false ||
			strpos($value, '(') !== false ||
			strpos($value, ')') !== false
		)
		return $_->depend->simple_parse($value, $check_com_user__user);
	switch ($value) {
		case 'verified':
			return !isset($_SESSION['user']->secret);
		case 'primary':
			return (isset($_SESSION['user']->group) && isset($_SESSION['user']->group->guid) && $_SESSION['user']->group->has_tag('enabled'));
		case 'secondary':
			if (!empty($_SESSION['user']->groups)) {
				foreach ($_SESSION['user']->groups as $cur_group) {
					if (isset($cur_group->guid) && $cur_group->has_tag('enabled'))
						return true;
				}
			}
			return false;
		case 'mailing':
			return ($_SESSION['user']->email && !$_->com_mailer->unsubscribe_query($_SESSION['user']->email));
		case 'email':
			return !empty($_SESSION['user']->email);
		case 'phone':
			return !empty($_SESSION['user']->phone);
		case 'address':
			if ($_SESSION['user']->address_type == 'us') {
				return (
					!empty($_SESSION['user']->address_1) &&
					!empty($_SESSION['user']->city) &&
					!empty($_SESSION['user']->state) &&
					!empty($_SESSION['user']->zip)
				);
			} elseif ($_SESSION['user']->address_type == 'international') {
				return !empty($_SESSION['user']->address_international);
			}
			return false;
	}
};

$_->depend->checkers['user'] = $check_com_user__user;