<?php
/**
 * com_hrm_issue_type class.
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
 * An employee issue type.
 *
 * @package Components\hrm
 */
class com_hrm_issue_type extends entity {
	protected $tags = array('com_hrm', 'issue_type');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->penalty = 0;
	}

	public static function etype() {
		return 'com_hrm_issue_type';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'issue type';
			case 'types':
				return 'issue types';
			case 'url_list':
				if (gatekeeper('com_hrm/listissuetypes'))
					return pines_url('com_hrm', 'issue/list');
				break;
			case 'icon':
				return 'picon-task-attention';
		}
		return null;
	}

	/**
	 * Delete the issue type.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted issue type $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the issue type.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		return parent::save();
	}
}