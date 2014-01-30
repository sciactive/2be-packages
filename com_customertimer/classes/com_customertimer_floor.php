<?php
/**
 * com_customertimer_floor class.
 *
 * @package Components\customertimer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A floorplan of stations.
 *
 * @package Components\customertimer
 */
class com_customertimer_floor extends entity {
	protected $tags = array('com_customertimer', 'floor');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->enabled = true;
		$this->stations = array();
		$this->active_stations = array();
	}

	public static function etype() {
		return 'com_customertimer_floor';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'timed floor';
			case 'types':
				return 'timed floors';
			case 'url_view':
				if (gatekeeper('com_customertimer/timefloor'))
					return pines_url('com_customertimer', 'timefloor', array('id' => $this->guid));
				break;
			case 'url_edit':
				if (gatekeeper('com_customertimer/editfloor'))
					return pines_url('com_customertimer', 'editfloor', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_customertimer/listfloors'))
					return pines_url('com_customertimer', 'listfloors');
				break;
			case 'icon':
				return 'picon-player-time';
		}
		return null;
	}

	/**
	 * Delete the floor.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted floor $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the floor.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the floor.
	 * @return module The form's module.
	 */
	public function print_form() {
		$module = new module('com_customertimer', 'form_floor', 'content');
		$module->entity = $this;

		return $module;
	}

	/**
	 * Print a form to time customers on the floor.
	 * @return module The form's module.
	 */
	public function print_timer() {
		global $pines;
		$module = new module('com_customertimer', 'form_floor_timer', 'content');
		$module->entity = $this;

		return $module;
	}
}