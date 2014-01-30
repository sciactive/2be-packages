<?php
/**
 * com_mailer_rendition class.
 *
 * @package Components\mailer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A rendition.
 *
 * @package Components\mailer
 */
class com_mailer_rendition extends entity {
	protected $tags = array('com_mailer', 'rendition');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->enabled = true;
		$this->conditions = array();
		$this->ac->other = 1;
	}

	public static function etype() {
		return 'com_mailer_rendition';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'rendition';
			case 'types':
				return 'renditions';
			case 'url_edit':
				if (gatekeeper('com_mailer/editrendition'))
					return pines_url('com_mailer', 'rendition/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_mailer/listrenditions'))
					return pines_url('com_mailer', 'rendition/list');
				break;
			case 'icon':
				return 'picon-internet-mail';
		}
		return null;
	}

	/**
	 * Delete the rendition.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted rendition $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the rendition.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the rendition.
	 * @return module The form's module.
	 */
	public function print_form() {
		$module = new module('com_mailer', 'rendition/form', 'content');
		$module->entity = $this;

		return $module;
	}

	/**
	 * Determine if this rendition is ready to use.
	 *
	 * This function will check the conditions of the rendition.
	 *
	 * @return bool True if the rendition is ready, false otherwise.
	 */
	public function ready() {
		if (!$this->enabled)
			return false;
		if (!$this->conditions)
			return true;
		global $pines;
		// Check that all conditions are met.
		foreach ($this->conditions as $cur_type => $cur_value) {
			if (!$pines->depend->check($cur_type, $cur_value))
				return false;
		}
		return true;
	}
}