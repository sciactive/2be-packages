<?php
/**
 * com_sales_return_checklist class.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A return checklist.
 *
 * @package Components\sales
 */
class com_sales_return_checklist extends entity {
	protected $tags = array('com_sales', 'return_checklist');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->enabled = true;
	}

	public static function etype() {
		return 'com_sales_return_checklist';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'return checklist';
			case 'types':
				return 'return checklists';
			case 'url_edit':
				if (gatekeeper('com_sales/editreturnchecklist'))
					return pines_url('com_sales', 'returnchecklist/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_sales/listreturnchecklists'))
					return pines_url('com_sales', 'returnchecklist/list');
				break;
			case 'icon':
				return 'picon-view-task';
		}
		return null;
	}

	/**
	 * Delete the return checklist.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted return checklist $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the return checklist.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the return checklist.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;
		$module = new module('com_sales', 'returnchecklist/form', 'content');
		$module->entity = $this;

		return $module;
	}
}