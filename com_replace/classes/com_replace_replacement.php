<?php
/**
 * com_replace_replacement class.
 *
 * @package Components\replace
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A replacement.
 *
 * @package Components\replace
 */
class com_replace_replacement extends entity {
	protected $tags = array('com_replace', 'replacement');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->enabled = true;
		$this->strings = array();
		$this->conditions = array();
	}

	public static function etype() {
		return 'com_replace_replacement';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'replacement';
			case 'types':
				return 'replacements';
			case 'url_edit':
				if (gatekeeper('com_replace/editreplacement'))
					return pines_url('com_replace', 'replacement/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_replace/listreplacements'))
					return pines_url('com_replace', 'replacement/list');
				break;
			case 'icon':
				return 'picon-edit-find-replace';
		}
		return null;
	}

	/**
	 * Delete the replacement.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted replacement $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the replacement.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the replacement.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;
		$module = new module('com_replace', 'replacement/form', 'content');
		$module->entity = $this;

		return $module;
	}

	/**
	 * Determine if this replacement should run.
	 *
	 * This function will check the conditions of the replacement. If the
	 * replacement is disabled or any of the conditions aren't met, it will
	 * return false.
	 *
	 * @return bool True if the replacement is ready, false otherwise.
	 */
	public function ready() {
		if (!$this->enabled)
			return false;
		if (!$this->conditions)
			return true;
		global $_;
		// Check that all conditions are met.
		foreach ($this->conditions as $cur_type => $cur_value) {
			if (!$_->depend->check($cur_type, $cur_value))
				return false;
		}
		return true;
	}
}