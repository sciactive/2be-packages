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
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A bonus for an employee.
 *
 * @package Components\hrm
 */
class com_hrm_adjustment extends entity {
	protected $tags = array('com_hrm', 'adjustment');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->comments = array();
	}

	public static function etype() {
		return 'com_hrm_adjustment';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "Adjustment $this->guid";
			case 'type':
				return 'adjustment';
			case 'types':
				return 'adjustments';
			case 'url_list':
				if (gatekeeper('com_hrm/listadjustments'))
					return pines_url('com_hrm', 'adjustment/list');
				break;
			case 'icon':
				return 'picon-accessories-calculator';
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
		pines_log("Deleted adjustment $this->guid.", 'notice');
		return true;
	}
}