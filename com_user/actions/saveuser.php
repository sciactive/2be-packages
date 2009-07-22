<?php
/**
 * Save changes to a user.
 *
 * @package XROOM
 * @subpackage com_user
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('X_RUN') or die('Direct access prohibited');

if ( empty($_REQUEST['username']) ) {
	display_error('Must specify username!');
	return;
}

if ( isset($_REQUEST['user_id']) ) {
	if ( !gatekeeper('com_user/edit') && (!gatekeeper('com_user/self') || ($_REQUEST['user_id'] != $_SESSION['user_id'])) ) {
		$config->user_manager->punt_user("You don't have necessary permission.", $config->template->url('com_user', 'manageusers', null, false));
		return;
	}
	$user = new user;
	$user = $config->user_manager->get_user($_REQUEST['user_id']);
	if ( is_null($user) ) {
		display_error('User doesn\'t exists!');
		return;
	}
	if ( $user->username != $_REQUEST['username'] ) {
		if ( is_null($config->user_manager->get_user_by_username($_REQUEST['username'])) ) {
			$user->username = $_REQUEST['username'];
		} else {
			display_error('Username ['.$_REQUEST['username'].'] already exists! Continuing with old username...');
		}
	}
	if ( !empty($_REQUEST['password']) ) $user->password($_REQUEST['password']);
} else {
	if ( !gatekeeper('com_user/new') ) {
		$config->user_manager->punt_user("You don't have necessary permission.", $config->template->url('com_user', 'manageusers', null, false));
		return;
	}
	if ( empty($_REQUEST['password']) && !$config->com_user->empty_pw ) {
		display_error('Must specify password!');
		return;
	}
	$user = new user;
	$user = $config->user_manager->new_user();
	if ( is_null($config->user_manager->get_user_by_username($_REQUEST['username'])) ) {
		$user->username = $_REQUEST['username'];
	} else {
		display_error('Username already exists!');
		return;
	}
	$user->password($_REQUEST['password']);
}

$user->name = $_REQUEST['name'];
$user->email = $_REQUEST['email'];

if ( gatekeeper('com_user/default_component') ) {
    if ( file_exists('components/'.$_REQUEST['default_component'].'/actions/default.php') ) {
        $user->default_component = $_REQUEST['default_component'];
    } else {
        display_error('Selected component does not support a default action.');
    }
}

// Go through a list of all groups, and assign them if they're selected.
/**
 * @todo Recode this when users can be limited to controlling users/groups below their level.
 */
if ( gatekeeper("com_user/assigng") && !empty($_REQUEST['groups']) ) {
    $groups = $config->entity_manager->get_entities_by_tags('com_user', 'group', group);
    $ugroups = $_REQUEST['groups'];
    array_walk($ugroups, 'intval');
    foreach ($groups as $cur_group) {
        if ( in_array($cur_group->guid, $ugroups) ) {
            $user->addgroup($cur_group->guid);
        } else {
            $user->delgroup($cur_group->guid);
        }
    }
}

if ( $_REQUEST['abilities'] === 'true' && gatekeeper("com_user/abilities") ) {
    $user->inherit_abilities = ($_REQUEST['inherit_abilities'] == 'ON' ? true : false);
	$sections = array('system');
	foreach ($config->components as $cur_component) {
		$sections[] = $cur_component;
	}
	foreach ($sections as $cur_section) {
		$section_abilities = $config->ability_manager->get_abilities($cur_section);
		if ( count($section_abilities) ) {
			foreach ($section_abilities as $cur_ability) {
				if ( isset($_REQUEST[$cur_section]) && (array_search($cur_ability['ability'], $_REQUEST[$cur_section]) !== false) ) {
					$user->grant($cur_section.'/'.$cur_ability['ability']);
				} else {
					$user->revoke($cur_section.'/'.$cur_ability['ability']);
				}
			}
		}
	}
}

$user->save();

display_notice('Saved user ['.$user->username.']');

$config->user_manager->list_users();
?>