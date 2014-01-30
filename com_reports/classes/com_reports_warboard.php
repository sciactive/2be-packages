<?php
/**
 * com_reports_warboard class.
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A list of employee contact information.
 *
 * @package Components\reports
 */
class com_reports_warboard extends entity {
	protected $tags = array('com_reports', 'warboard');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->company_name = 'Company Name';
		$this->columns = 5;
		$this->positions = array();
		$this->locations = array();
		$this->important = array();
		$this->hq = $_SESSION['user']->group;
	}

	public static function etype() {
		return 'com_reports_warboard';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->title;
			case 'type':
				return 'warboard';
			case 'types':
				return 'warboards';
			case 'url_view':
				if (gatekeeper('com_reports/warboard'))
					return pines_url('com_reports', 'warboard');
				break;
			case 'url_edit':
				if (gatekeeper('com_reports/editwarboard'))
					return pines_url('com_reports', 'editwarboard');
				break;
			case 'icon':
				return 'picon-view-list-details';
		}
		return null;
	}

	/**
	 * Delete the warboard.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted warboard [$this->title].", 'notice');
		return true;
	}

	/**
	 * Print a form to edit the warboard.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $pines;

		$form = new module('com_reports', 'form_warboard', 'content');
		$form->entity = $this;
		$form->groups = $pines->com_user->get_groups();
		$employees = $pines->com_hrm->get_employees();
		$form->job_titles = array();
		foreach ($employees as $cur_employee) {
			if ($cur_employee->job_title != '' && !in_array($cur_employee->job_title, $form->job_titles))
				$form->job_titles[] = $cur_employee->job_title;
		}
	}

	/**
	 * Show the company warboard.
	 * @return module The module.
	 */
	public function show() {
		global $pines;

		$head = new module('com_reports', 'warboard_head', 'head');
		$module = new module('com_reports', 'warboard', 'content');
		$module->entity = $this;
		/*
		//$pines->com_user->group_sort($this->locations, 'name');
		foreach ($this->locations as $cur_l) {
			echo "{$cur_l->parent->name} : {$cur_l->name}<br />";
		}
		echo "<br /><br /><br />";
		*/
		$pines->entity_manager->psort($this->locations, 'name', 'parent');
		/*
		foreach ($this->locations as $cur_l) {
			echo "{$cur_l->parent->name} : {$cur_l->name}<br />";
		}
		*/
		$pines->entity_manager->sort($this->important, 'name');

		return $module;
	}
}