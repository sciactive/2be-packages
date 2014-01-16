<?php
/**
 * com_configure_condition class.
 *
 * @package Components\configure
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A conditional configuration.
 *
 * @package Components\configure
 */
class com_configure_condition extends entity {
	/**
	 * Load a conditional configuration.
	 * @param int $id The ID of the configuration to load, 0 for a new configuration.
	 */
	public function __construct($id = 0) {
		parent::__construct();
		$this->add_tag('com_configure', 'condition');
		if ($id > 0) {
			global $pines;
			$entity = $pines->entity_manager->get_entity(array('class' => get_class($this)), array('&', 'guid' => $id, 'tag' => $this->tags));
			if (isset($entity)) {
				$this->guid = $entity->guid;
				$this->tags = $entity->tags;
				$this->put_data($entity->get_data(), $entity->get_sdata());
				return;
			}
		}
		// Defaults.
		$this->conditions = array();
		$this->sys_config = array();
		$this->com_config = array();
		$this->is_com_configure_condition = true;
	}

	/**
	 * Create a new instance.
	 * @return com_configure_condition The new instance.
	 */
	public static function factory() {
		global $pines;
		$class = get_class();
		$args = func_get_args();
		$entity = new $class($args[0]);
		$pines->hook->hook_object($entity, $class.'->', false);
		return $entity;
	}

	public static function etype() {
		return 'com_configure_condition';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'condition';
			case 'types':
				return 'conditions';
			case 'url_edit':
				if (gatekeeper('com_configure/edit'))
					return pines_url('com_configure', 'condition/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_configure/view'))
					return pines_url('com_configure', 'list', array('percondition' => 'true'));
				break;
			case 'icon':
				return 'picon-preferences-other';
		}
		return null;
	}

	/**
	 * Delete the condition.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted condition $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the condition.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the condition.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $pines;
		$module = new module('com_configure', 'condition_form', 'content');
		$module->entity = $this;

		return $module;
	}
}