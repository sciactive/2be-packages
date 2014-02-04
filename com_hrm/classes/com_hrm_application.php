<?php
/**
 * com_hrm_application class.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * An application that has been submitted.
 *
 * @package Components\hrm
 */
class com_hrm_application extends entity {
	protected $tags = array('com_hrm', 'application');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->address_type = 'us';
		$this->education = $this->employment = array();
		$this->references = array(
			array(
				'name' => 'Name',
				'phone' => 'Phone Number',
				'occupation' => 'Occupation'
			),
			array(
				'name' => 'Name',
				'phone' => 'Phone Number',
				'occupation' => 'Occupation'
			),
			array(
				'name' => 'Name',
				'phone' => 'Phone Number',
				'occupation' => 'Occupation'
			)
		);
		$this->status = 'pending';
	}

	public static function etype() {
		return 'com_hrm_application';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "Application $this->name_first $this->name_last";
			case 'type':
				return 'employement application';
			case 'types':
				return 'employement applications';
			case 'url_view':
				if (gatekeeper('com_hrm/listapplications'))
					return pines_url('com_hrm', 'application/view', array('id' => $this->guid));
				break;
			case 'url_edit':
				if (gatekeeper('com_hrm/editapplication') && isset($this->user->guid) && $this->user->is($_SESSION['user']))
					return pines_url('com_hrm', 'application/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_hrm/listapplications'))
					return pines_url('com_hrm', 'application/list');
				break;
			case 'icon':
				return 'picon-story-editor';
		}
		return null;
	}

	/**
	 * Delete the application.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted employement application $this->name_first $this->name_last.", 'notice');
		return true;
	}

	/**
	 * Print a form to apply for employment.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;
		$module = new module('com_hrm', 'application/form', 'content');
		$module->entity = $this;

		return $module;
	}

	/**
	 * Print a form to view an employment application.
	 * @return module The form's module.
	 */
	public function view_application() {
		global $_;
		$module = new module('com_hrm', 'application/view', 'content');
		$module->entity = $this;

		return $module;
	}
}