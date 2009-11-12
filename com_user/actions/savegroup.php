<?php
/**
 * Save changes to a group.
 *
 * @package Pines
 * @subpackage com_user
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

$pass = true;

if ( empty($_REQUEST['groupname']) ) {
	display_error('Must specify groupname!');
	$pass = false;
}

if ( isset($_REQUEST['id']) ) {
	if ( !gatekeeper('com_user/editg') ) {
	$config->user_manager->punt_user("You don't have necessary permission.", pines_url('com_user', 'managegroups', null, false));
	return;
	}
	$group = $config->user_manager->get_group($_REQUEST['id']);
	if ( is_null($group) ) {
	display_error('Group doesn\'t exists!');
	$pass = false;
	}
	if ( $group->groupname != $_REQUEST['groupname'] ) {
	if ( is_null($config->user_manager->get_group_by_groupname($_REQUEST['groupname'])) ) {
		$group->groupname = $_REQUEST['groupname'];
	} else {
		display_error('Groupname ['.$_REQUEST['groupname'].'] already exists! Continuing with old groupname...');
	}
	}
} else {
	if ( !gatekeeper('com_user/newg') ) {
	$config->user_manager->punt_user("You don't have necessary permission.", pines_url('com_user', 'managegroups', null, false));
	return;
	}
	$group = new group;
	if ( is_null($config->user_manager->get_group_by_groupname($_REQUEST['groupname'])) ) {
	$group->groupname = $_REQUEST['groupname'];
	} else {
	display_error('Groupname already exists!');
	$pass = false;
	}
}

$group->name = $_REQUEST['name'];
$group->email = $_REQUEST['email'];

/**
 * @todo Check if the selected parent is a descendant of this group.
 */
// Clean the requested parent. Make sure it's both valid and not the same group.
if ( $_REQUEST['parent'] == 'none' ) {
	$parent = NULL;
} else {
	if ( is_null($config->user_manager->get_group($_REQUEST['parent'])) || $_REQUEST['parent'] == $group->guid ) {
	display_error('Parent is not valid!');
	$pass = false;
	} else {
	$parent = $_REQUEST['parent'];
	}
}
$group->parent = $parent;

if ( $_REQUEST['abilities'] === 'true' && gatekeeper("com_user/abilities") ) {
	$sections = array('system');
	foreach ($config->components as $cur_component) {
	$sections[] = $cur_component;
	}
	foreach ($sections as $cur_section) {
	$section_abilities = $config->ability_manager->get_abilities($cur_section);
	if ( count($section_abilities) ) {
		foreach ($section_abilities as $cur_ability) {
		if ( isset($_REQUEST[$cur_section]) && (array_search($cur_ability['ability'], $_REQUEST[$cur_section]) !== false) ) {
			$group->grant($cur_section.'/'.$cur_ability['ability']);
		} else {
			$group->revoke($cur_section.'/'.$cur_ability['ability']);
		}
		}
	}
	}
}

if (!$pass) {
	$module = $config->user_manager->print_group_form('com_user', 'savegroup');
	$module->entity = $group;
	return;
}

if ($group->save()) {
	display_notice('Saved group ['.$group->groupname.']');
	pines_log('Saved group ['.$group->groupname.']');
} else {
	display_error('Error saving group. Do you have permission?');
}

$config->user_manager->list_groups();
?>