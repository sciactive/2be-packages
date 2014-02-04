<?php
/**
 * group class.
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
 * WonderPHP system groups.
 *
 * @package Components\user
 * @property int $guid The GUID of the group.
 * @property string $groupname The group's groupname.
 * @property string $name The group's name.
 * @property string $email The group's email address.
 * @property string $phone The group's telephone number.
 * @property string $address_type The group's address type. "us" or "international".
 * @property string $address_1 The group's address line 1 for US addresses.
 * @property string $address_2 The group's address line 2 for US addresses.
 * @property string $city The group's city for US addresses.
 * @property string $state The group's state abbreviation for US addresses.
 * @property string $zip The group's ZIP code for US addresses.
 * @property string $address_international The group's full address for international addresses.
 * @property group $parent The group's parent.
 */
class group extends able_object implements group_interface {
	protected $tags = array('com_user', 'group', 'enabled');

	public function __construct($id = 0) {
		if ($id > 0 || (string) $id === $id) {
			global $_;
			if ((int) $id === $id)
				$entity = $_->entity_manager->get_entity(array('class' => get_class($this)), array('&', 'guid' => $id, 'tag' => array('com_user', 'group')));
			else
				$entity = $_->entity_manager->get_entity(array('class' => get_class($this)), array('&', 'tag' => array('com_user', 'group'), 'data' => array('groupname', $id)));
			if (isset($entity)) {
				$this->guid = $entity->guid;
				$this->tags = $entity->tags;
				$this->put_data($entity->get_data(), $entity->get_sdata());
				return;
			}
		}
		// Defaults.
		$this->abilities = array();
		$this->conditions = array();
		$this->address_type = 'us';
		$this->attributes = array();
	}

	public static function etype() {
		return 'group';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "$this->name [$this->groupname]";
			case 'type':
				return 'group';
			case 'types':
				return 'groups';
			case 'url_edit':
				if (gatekeeper('com_user/editgroup'))
					return pines_url('com_user', 'editgroup', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_user/listgroups'))
					return pines_url('com_user', 'listgroups');
				break;
			case 'icon':
				return 'picon-system-users';
			default:
				return parent::info($type);
		}
		return null;
	}

	/**
	 * Return the entity helper module.
	 * @return module Entity helper module.
	 */
	public function helper() {
		return new module('com_user', 'helper_group');
	}

	public function is_descendant($group = null) {
		if (is_numeric($group))
			$group = group::factory((int) $group);
		if (!isset($group->guid))
			return false;
		// Check to see if the group is a descendant of the given group.
		if (!isset($this->parent))
			return false;
		if ($this->parent->is($group))
			return true;
		if ($this->parent->is_descendant($group))
			return true;
		return false;
	}

	public function is_descendent($group = null) {
		return $this->is_descendant($group);
	}

	public function delete() {
		global $_;
		$entities = $_->entity_manager->get_entities(
				array('class' => group),
				array('&',
					'tag' => array('com_user', 'group'),
					'ref' => array('parent', $this)
				)
			);
		foreach ($entities as $cur_group) {
			if ( !$cur_group->delete() )
				return false;
		}
		if (!parent::delete())
			return false;
		pines_log("Deleted group $this->name [$this->groupname].", 'notice');
		return true;
	}

	public function disable() {
		$this->remove_tag('enabled');
	}

	public function enable() {
		$this->add_tag('enabled');
	}

	public function save() {
		if (!isset($this->groupname))
			return false;
		return parent::save();
	}

	public function get_children() {
		global $_;
		$return = (array) $_->entity_manager->get_entities(
				array('class' => group),
				array('&',
					'tag' => array('com_user', 'group', 'enabled'),
					'ref' => array('parent', $this)
				)
			);
		return $return;
	}

	public function get_descendants($and_self = false) {
		global $_;
		$return = array();
		$entities = $_->entity_manager->get_entities(
				array('class' => group),
				array('&',
					'tag' => array('com_user', 'group', 'enabled'),
					'ref' => array('parent', $this)
				)
			);
		foreach ($entities as $entity) {
			$child_array = $entity->get_descendants(true);
			$return = array_merge($return, $child_array);
		}
		$hooked = $this;
		$class = get_class();
		$_->hook->hook_object($hooked, $class.'->', false);
		if ($and_self)
			$return[] = $hooked;
		return $return;
	}

	public function get_descendents($and_self = false) {
		return $this->get_descendants($and_self);
	}

	public function get_level() {
		$group = $this;
		$level = 0;
		while (isset($group->parent) && $group->parent->has_tag('enabled')) {
			$level++;
			$group = $group->parent;
		}
		return $level;
	}

	public function get_logo($full = false) {
		global $_;
		if (isset($this->logo))
			return $full ? $_->uploader->url($_->uploader->real($this->logo), true) : $this->logo;
		if (isset($this->parent) && $this->parent->has_tag('enabled'))
			return $this->parent->get_logo($full);
		return ($full ? $_->config->full_location : $_->config->location)."{$_->config->upload_location}logos/default_logo.png";
	}

	public function get_users($descendants = false) {
		global $_;
		if ($descendants)
			$groups = $this->get_descendants();
		else
			$groups = array();
		$groups[] = $this;
		$return = $_->entity_manager->get_entities(
				array('class' => user),
				array('&',
					'tag' => array('com_user', 'user', 'enabled')
				),
				array('|',
					'ref' => array(
						array('group', $groups),
						array('groups', $groups)
					)
				)
			);
		return $return;
	}

	public function print_form() {
		global $_;
		$module = new module('com_user', 'form_group', 'content');
		$module->entity = $this;
		$module->display_username = gatekeeper('com_user/usernames');
		$module->display_enable = gatekeeper('com_user/enabling');
		$module->display_default = gatekeeper('com_user/defaultgroups');
		$module->display_abilities = gatekeeper('com_user/abilities');
		$module->display_conditions = gatekeeper('com_user/conditions');
		$module->sections = array('system');
		$module->group_array = $_->user_manager->get_groups();
		foreach ($_->components as $cur_component)
			$module->sections[] = $cur_component;

		return $module;
	}
}