<?php
/**
 * com_configure class.
 *
 * @package Components\configure
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_configure main class.
 *
 * Manages WonderPHP configuration.
 *
 * @package Components\configure
 */
class com_configure extends component implements configurator_interface {
	/**
	 * An array of config files found on the system.
	 *
	 * Each key is the name of the component to which the config/info files in
	 * its value belong.
	 *
	 * @var array $component_files
	 */
	public $component_files = array();

	/**
	 * Fills the $component_files array.
	 */
	public function __construct() {
		global $pines;
		$this->component_files['system'] = array('defaults' => 'system/defaults.php', 'config' => 'system/config.php', 'info' => 'system/info.php');
		foreach ($pines->all_components as $cur_component) {
			$cur_dir = (substr($cur_component, 0, 4) != 'tpl_') ? 'components' : 'templates';
			if (in_array($cur_component, $pines->components)) {
				$cur_files = array('defaults' => "$cur_dir/$cur_component/defaults.php", 'config' => "$cur_dir/$cur_component/config.php", 'info' => "$cur_dir/$cur_component/info.php");
			} else {
				$cur_files = array('defaults' => "$cur_dir/.$cur_component/defaults.php", 'config' => "$cur_dir/.$cur_component/config.php", 'info' => "$cur_dir/.$cur_component/info.php");
			}
			if (file_exists($cur_files['defaults']) || file_exists($cur_files['info']))
				$this->component_files[$cur_component] = $cur_files;
		}
	}

	/**
	 * Disables a component.
	 *
	 * This function renames the component's directory by adding a dot (.) in
	 * front of the name. This causes WonderPHP to ignore the component.
	 *
	 * @param string $component The name of the component.
	 * @return bool True on success, false on failure.
	 */
	public function disable_component($component) {
		global $pines;
		if (!in_array($component, $pines->all_components)) {
			pines_log("Failed to disable component $component. Component isn't installed", 'error');
			return false;
		}
		$cur_dir = (substr($component, 0, 4) != 'tpl_') ? 'components' : 'templates';
		if (in_array($component, $pines->components) && rename("$cur_dir/$component", "$cur_dir/.$component")) {
			pines_log("Disabled component $component.", 'notice');
			return true;
		} else {
			pines_log("Failed to disable component $component.", 'error');
			return false;
		}
	}

	/**
	 * Enables a component.
	 *
	 * This function renames the component's directory by removing the dot (.)
	 * in front of the name. This causes WonderPHP to recognize the component.
	 *
	 * @param string $component The name of the component.
	 * @return bool True on success, false on failure.
	 */
	public function enable_component($component) {
		global $pines;
		if (!in_array($component, $pines->all_components)) {
			pines_log("Failed to enable component $component. Component isn't installed", 'error');
			return false;
		}
		$cur_dir = (substr($component, 0, 4) != 'tpl_') ? 'components' : 'templates';
		if (!in_array($component, $pines->components) && rename("$cur_dir/.$component", "$cur_dir/$component")) {
			pines_log("Enabled component $component.", 'notice');
			return true;
		} else {
			pines_log("Failed to enable component $component.", 'error');
			return false;
		}
	}

	/**
	 * Creates and attaches a module which lists configurable components.
	 * @return module The module.
	 */
	public function list_components() {
		global $pines;
		$module = new module('com_configure', 'list', 'content');

		$module->components = array();
		$module->components[] = configurator_component::factory('system');
		$module->per_user = false;
		foreach ($pines->all_components as $cur_component) {
			$module->components[] = configurator_component::factory($cur_component);
		}

		return $module;
	}

	/**
	 * Creates and attaches a module which lists per user config components.
	 * @param user|group|com_configure_condition &$condition_obj The user, group, or conditional object which is being configured.
	 * @return module The module.
	 */
	public function list_components_peruser(&$condition_obj = null) {
		global $pines;
		$module = new module('com_configure', 'list', 'content');

		$module->components = array();
		$module->components[] = configurator_component::factory('system');
		$module->user = $condition_obj;
		if ($condition_obj->is_com_configure_condition) {
			$module->per_condition = true;
			$module->conditions = $pines->entity_manager->get_entities(array('class' => com_configure_condition), array('&', 'tag' => array('com_configure', 'condition')));
			$pines->entity_manager->sort($module->conditions, 'name');
		} else {
			$module->per_user = true;
			$module->groups = $pines->user_manager->get_groups();
			usort($module->groups, array($this, 'sort_groups'));
			$module->users = $pines->user_manager->get_users();
			usort($module->users, array($this, 'sort_users'));
		}
		foreach ($pines->all_components as $cur_component) {
			$module->components[] = configurator_component::factory($cur_component);
		}
		foreach ($module->components as &$cur_component) {
			$cur_component->set_per_user($condition_obj);
		}

		return $module;
	}

	/**
	 * Load a config array built from users/groups.
	 * @param array $sys_array The system config array to load.
	 * @param array $com_array The component config array to load.
	 */
	public function load_per_user_array($sys_array, $com_array) {
		global $pines;
		if ($sys_array) {
			$conf = include('system/defaults.php');
			foreach ($conf as $key => $value) {
				if (!$value['peruser'] || !isset($sys_array[$value['name']]))
					continue;
				$name = $value['name'];
				$pines->config->$name = $sys_array[$name];
			}
		}
		if ($com_array) {
			foreach ($com_array as $cur_com => $cur_array) {
				if (!$this->component_files[$cur_com]['defaults'])
					continue;
				$conf = include($this->component_files[$cur_com]['defaults']);
				foreach ($conf as $key => $value) {
					if (!$value['peruser'] || !isset($cur_array[$value['name']]))
						continue;
					$name = $value['name'];
					$pines->config->$cur_com->$name = $cur_array[$name];
				}
			}
		}
	}

	/**
	 * Sort groups.
	 * @param group $a Group.
	 * @param group $b Group.
	 * @return bool Group order.
	 */
	private function sort_groups($a, $b) {
		$aname = empty($a->name) ? $a->groupname : $a->name;
		$bname = empty($b->name) ? $b->groupname : $b->name;
		return strtolower($aname) > strtolower($bname);
	}

	/**
	 * Sort users.
	 * @param group $a User.
	 * @param group $b User.
	 * @return bool User order.
	 */
	private function sort_users($a, $b) {
		$aname = empty($a->name) ? $a->username : $a->name;
		$bname = empty($b->name) ? $b->username : $b->name;
		return strtolower($aname) > strtolower($bname);
	}
}