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
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A conditional configuration.
 *
 * @package Components\configure
 */
class com_configure_condition extends entity {
	protected $tags = array('com_configure', 'condition');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->conditions = array();
		$this->sys_config = array();
		$this->com_config = array();
		$this->is_com_configure_condition = true;
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
		global $_;
		$module = new module('com_configure', 'condition_form', 'content');
		$module->entity = $this;

		return $module;
	}
}