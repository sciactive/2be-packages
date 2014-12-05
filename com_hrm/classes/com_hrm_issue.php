<?php
/**
 * com_hrm_issue class.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * An issue filed for an employee.
 *
 * @package Components\hrm
 */
class com_hrm_issue extends Entity {
	const etype = 'com_hrm_issue';
	protected $tags = array('com_hrm', 'issue');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->comments = array();
		$this->status = 'unresolved';
		$this->quantity = 1;
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "Issue $this->guid";
			case 'type':
				return 'issue';
			case 'types':
				return 'issues';
			case 'icon':
				return 'picon-task-attention';
		}
		return null;
	}

	/**
	 * Delete the issue.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted issue $this->guid ($this->name).", 'notice');
		return true;
	}

	/**
	 * Save the issue.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->issue_id))
			$this->issue_id = 0;
		return parent::save();
	}
}