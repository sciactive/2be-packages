<?php
/**
 * com_hrm_bonus class.
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
 * A bonus for an employee.
 *
 * @package Components\hrm
 */
class com_hrm_bonus extends entity {
	protected $tags = array('com_hrm', 'bonus');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->comments = array();
	}

	public static function etype() {
		return 'com_hrm_bonus';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "Bonus $this->guid";
			case 'type':
				return 'bonus';
			case 'types':
				return 'bonuses';
			case 'url_list':
				if (gatekeeper('com_hrm/listbonuses'))
					return pines_url('com_hrm', 'bonus/list');
				break;
			case 'icon':
				return 'picon-get-hot-new-stuff';
		}
		return null;
	}

	/**
	 * Delete the bonus.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted bonus $this->guid.", 'notice');
		return true;
	}
}