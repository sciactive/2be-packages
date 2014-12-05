<?php
/**
 * Hook entity functions for access control.
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
 * Check for the skip access control option.
 *
 * @param array &$array An array of arguments. (The options array and the selectors.)
 * @param mixed $name Unused.
 * @param mixed &$object Unused.
 * @param mixed &$function Unused.
 * @param array &$data The callback data array.
 */
function com_user__getEntities(&$array, $name, &$object, &$function, &$data) {
	if ($array[0]['skip_ac'])
		$data['com_user_skip_ac'] = true;
}

/**
 * Filter entities being deleted for user permissions.
 *
 * @param array &$array An array of an entity or guid.
 */
function com_user__check_permissions_delete(&$array) {
	global $_;
	$entity = $array[0];
	if ((int) $entity === $entity)
		$entity = $_->nymph->getEntity($array[0]);
	if ((object) $entity !== $entity) {
		$array = false;
		return;
	}
	// Test for permissions.
	if (!$_->user_manager->check_permissions($entity, 3))
		$array = false;
}

/**
 * Filter entities being returned for user permissions.
 *
 * @param array &$array An array of either an entity or another array of entities.
 * @param mixed $name Unused.
 * @param mixed &$object Unused.
 * @param mixed &$function Unused.
 * @param array &$data The callback data array.
 */
function com_user__check_permissions_return(&$array, $name, &$object, &$function, &$data) {
	global $_;
	if ($data['com_user_skip_ac'])
		return;
	if ((array) $array[0] === $array[0]) {
		$is_array = true;
		$entities = &$array[0];
	} else {
		$is_array = false;
		$entities = &$array;
	}
	foreach ($entities as $key => &$cur_entity) {
		// Test for permissions.
		if (!$_->user_manager->check_permissions($cur_entity, 1))
			unset($entities[$key]);
	}
	unset($cur_entity);
}

/**
 * Filter entities being saved for user permissions.
 *
 * @param array &$array An array of an entity.
 */
function com_user__check_permissions_save(&$array) {
	global $_;
	$entity = $array[0];
	if ((object) $entity !== $entity) {
		$array = false;
		return;
	}
	// Test for permissions.
	if (!$_->user_manager->check_permissions($entity, 2))
		$array = false;
}

/**
 * Add the current user's "user", "group", and access control to a new entity.
 *
 * This occurs right before an entity is saved. It only alters the entity if:
 * - There is a user logged in.
 * - The entity is new (doesn't have a GUID.)
 * - The entity is not a user or group.
 *
 * If you want a new entity to have a different user and/or group than the
 * current user, you must first save it to the database, then change the
 * user/group, then save it again.
 *
 * Default access control is
 * - user = 3
 * - group = 3
 * - other = 0
 *
 * @param array &$array An array of either an entity or another array of entities.
 */
function com_user__add_access(&$array) {
	if ((object) $_SESSION['user'] === $_SESSION['user'] &&
		!isset($array[0]->guid) &&
		!$array[0]->hasTag('com_user', 'user') &&
		!$array[0]->hasTag('com_user', 'group') ) {
		$array[0]->user = $_SESSION['user'];
		$array[0]->group = $_SESSION['user']->group;
		if ((object) $array[0]->ac !== $array[0]->ac)
			$array[0]->ac = (object) array();
		if (!isset($array[0]->ac->user))
			$array[0]->ac->user = 3;
		if (!isset($array[0]->ac->group))
			$array[0]->ac->group = 3;
		if (!isset($array[0]->ac->other))
			$array[0]->ac->other = 0;
	}
}

foreach (array('$_->nymph->getEntity', '$_->nymph->getEntities') as $cur_hook) {
	$_->hook->add_callback($cur_hook, -10, 'com_user__getEntities');
	$_->hook->add_callback($cur_hook, 10, 'com_user__check_permissions_return');
}
unset ($cur_hook);

$_->hook->add_callback('$_->nymph->saveEntity', -100, 'com_user__add_access');
$_->hook->add_callback('$_->nymph->saveEntity', -99, 'com_user__check_permissions_save');

foreach (array('$_->nymph->deleteEntity', '$_->nymph->deleteEntityByID') as $cur_hook)
	$_->hook->add_callback($cur_hook, -99, 'com_user__check_permissions_delete');

unset ($cur_hook);