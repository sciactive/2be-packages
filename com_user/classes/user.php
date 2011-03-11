<?php
/**
 * user class.
 *
 * @package Pines
 * @subpackage com_user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

/**
 * Pines system users.
 *
 * @package Pines
 * @subpackage com_user
 */
class user extends able_object implements user_interface {
	public function __construct($id = 0) {
		parent::__construct();
		$this->add_tag('com_user', 'user', 'enabled');
		// Defaults.
		$this->abilities = array();
		$this->groups = array();
		$this->inherit_abilities = true;
		$this->address_type = 'us';
		$this->addresses = array();
		$this->attributes = array();
		if ($id > 0 || (string) $id === $id) {
			global $pines;
			if ((int) $id === $id) {
				$entity = $pines->entity_manager->get_entity(array('class' => get_class($this)), array('&', 'guid' => $id, 'tag' => array('com_user', 'user')));
			} else {
				$entity = $pines->entity_manager->get_entity(array('class' => get_class($this)), array('&', 'tag' => array('com_user', 'user'), 'strict' => array('username', (string) $id)));
			}
			if (!isset($entity))
				return;
			$this->guid = $entity->guid;
			$this->tags = $entity->tags;
			$this->put_data($entity->get_data(), $entity->get_sdata());
		}
	}

	/**
	 * Create a new instance.
	 *
	 * @param int|string $id The ID or username of the user to load, 0 for a new user.
	 * @return user A user instance.
	 */
	public static function factory($id = 0) {
		global $pines;
		$class = get_class();
		$args = func_get_args();
		$entity = new $class($args[0]);
		$pines->hook->hook_object($entity, $class.'->', false);
		return $entity;
	}

	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted user $this->name [$this->username].", 'notice');
		return true;
	}

	public function disable() {
		$this->remove_tag('enabled');
	}

	public function enable() {
		$this->add_tag('enabled');
	}

	public function save() {
		if (!isset($this->username))
			return false;
		return parent::save();
	}

	public function print_form() {
		global $pines;
		$module = new module('com_user', 'form_user', 'content');
		$module->entity = $this;
		$module->display_username = gatekeeper('com_user/usernames');
		$module->display_enable = gatekeeper('com_user/enabling');
		$module->display_pin = gatekeeper('com_user/assignpin');
		$module->display_groups = gatekeeper('com_user/assigngroup');
		$module->display_abilities = gatekeeper('com_user/abilities');
		$module->sections = array('system');
		$highest_parent = $pines->config->com_user->highest_primary;
		if ($highest_parent == 0) {
			$module->group_array_primary = $pines->user_manager->get_groups();
		} elseif ($highest_parent < 0) {
			$module->group_array_primary = array();
		} else {
			$highest_parent = group::factory($highest_parent);
			if (!isset($highest_parent->guid)) {
				$module->group_array_primary = array();
			} else {
				$module->group_array_primary = $highest_parent->get_descendents();
			}
		}
		$highest_parent = $pines->config->com_user->highest_secondary;
		if ($highest_parent == 0) {
			$module->group_array_secondary = $pines->user_manager->get_groups();
		} elseif ($highest_parent < 0) {
			$module->group_array_secondary = array();
		} else {
			$highest_parent = group::factory($highest_parent);
			if (!isset($highest_parent->guid)) {
				$module->group_array_secondary = array();
			} else {
				$module->group_array_secondary = $highest_parent->get_descendents();
			}
		}
		foreach ($pines->components as $cur_component) {
			$module->sections[] = $cur_component;
		}

		return $module;
	}

	/**
	 * Print a registration form for the user to fill out.
	 *
	 * @return module The form's module.
	 */
	public function print_register() {
		global $pines;
		$module = new module('com_user', 'form_register', 'content');
		$module->entity = $this;
		foreach ($pines->components as $cur_component)
			$module->sections[] = $cur_component;

		return $module;
	}
	
	public function add_group($group) {
		if ( !$group->in_array($this->groups) ) {
			$this->groups[] = $group;
			return $this->groups;
		} else {
			return true;
		}
	}

	public function check_password($password) {
		global $pines;
		if (!isset($this->salt)) {
			$pass = ($this->password == $password);
			$cur_type = 'salt';
		} elseif ($this->salt == '7d5bc9dc81c200444e53d1d10ecc420a') {
			$pass = ($this->password == md5($password.$this->salt));
			$cur_type = 'digest';
		} else {
			$pass = ($this->password == md5($password.$this->salt));
			$cur_type = 'salt';
		}
		if ($pass && $cur_type != $pines->config->com_user->pw_method) {
			switch ($pines->config->com_user->pw_method) {
				case 'plain':
					unset($this->salt);
					$this->password = $password;
					break;
				case 'salt':
					$this->salt = md5(rand());
					$this->password = md5($password.$this->salt);
					break;
				case 'digest':
				default:
					$this->salt = '7d5bc9dc81c200444e53d1d10ecc420a';
					$this->password = md5($password.$this->salt);
					break;
			}
			$this->save();
		}
		return $pass;
	}

	/**
	 * Check the given client hash and server challenge using SAWASC.
	 *
	 * @param string $ClientHash The hash provided by the client.
	 * @param string $ServerCB The challenge block generated by the server.
	 * @param string $algo Hash algorithm. Check hash_algos().
	 * @return bool True if the hashes match, otherwise false.
	 */
	public function check_sawasc($ClientHash, $ServerCB, $algo) {
		if ($this->salt == '7d5bc9dc81c200444e53d1d10ecc420a')
			$input = $this->password;
		else
			$input = md5($this->password.'7d5bc9dc81c200444e53d1d10ecc420a');
		$ServerComb = $ServerCB.$input;
		$ServerHash = hash($algo, $ServerComb);
		return ($ClientHash === $ServerHash);
	}

	public function del_group($group) {
		if ( $group->in_array($this->groups) ) {
			foreach ($this->groups as $key => $cur_group) {
				if ($group->is($cur_group))
					unset($this->groups[$key]);
			}
			return $this->groups;
		} else {
			return true;
		}
	}

	public function in_group($group = null) {
		if (is_numeric($group))
			$group = group::factory((int) $group);
		if (!isset($group->guid))
			return false;
		return ($group->in_array($this->groups) || $group->is($this->group));
	}

	public function is_descendent($group = null) {
		if (is_numeric($group))
			$group = group::factory((int) $group);
		if (!isset($group->guid))
			return false;
		// Check to see if the user is in a descendent group of the given group.
		if (isset($this->group) && $this->group->is_descendent($group))
			return true;
		foreach ($this->groups as $cur_group) {
			if ($cur_group->is_descendent($group))
				return true;
		}
		return false;
	}

	public function password($password) {
		global $pines;
		switch ($pines->config->com_user->pw_method) {
			case 'plain':
				unset($this->salt);
				return $this->password = $password;
				break;
			case 'salt':
				$this->salt = md5(rand());
				return $this->password = md5($password.$this->salt);
				break;
			case 'digest':
			default:
				$this->salt = '7d5bc9dc81c200444e53d1d10ecc420a';
				return $this->password = md5($password.$this->salt);
				break;
		}
	}

	/**
	 * First checks if the user has a timezone set, then the primary group, then
	 * the secondary groups, then the system default. The first timezone found
	 * is returned.
	 */
	public function get_timezone($return_date_time_zone_object = false) {
		global $pines;
		if (!empty($this->timezone))
			return $return_date_time_zone_object ? new DateTimeZone($this->timezone) : $this->timezone;
		if (isset($this->group) && !empty($this->group->timezone))
			return $return_date_time_zone_object ? new DateTimeZone($this->group->timezone) : $this->group->timezone;
		foreach($this->groups as $cur_group) {
			if (!empty($cur_group->timezone))
				return $return_date_time_zone_object ? new DateTimeZone($cur_group->timezone) : $cur_group->timezone;
		}
		return $return_date_time_zone_object ? new DateTimeZone($pines->config->timezone) : $pines->config->timezone;
	}
}

?>