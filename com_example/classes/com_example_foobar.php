<?php
/**
 * com_example_foobar class.
 *
 * @package Components\example
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A foobar.
 *
 * @package Components\example
 */
class com_example_foobar extends entity {
	protected $tags = array('com_example', 'foobar');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->enabled = true;
		$this->attributes = array();
	}

	public static function etype() {
		return 'com_example_foobar';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'foobar';
			case 'types':
				return 'foobars';
			case 'url_edit':
				if (gatekeeper('com_example/editfoobar'))
					return pines_url('com_example', 'foobar/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_example/listfoobars'))
					return pines_url('com_example', 'foobar/list');
				break;
			case 'icon':
				return 'picon-view-pim-journal';
		}
		return null;
	}

	/**
	 * Delete the foobar.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted foobar $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the foobar.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the foobar.
	 * @return module The form's module.
	 */
	public function print_form() {
		$module = new module('com_example', 'foobar/form', 'content');
		$module->entity = $this;

		return $module;
	}
}