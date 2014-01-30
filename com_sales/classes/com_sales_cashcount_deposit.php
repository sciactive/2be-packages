<?php
/**
 * com_sales_cashcount_deposit class.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A deposit off of skims from a cash count.
 *
 * @package Components\sales
 */
class com_sales_cashcount_deposit extends entity {
	protected $tags = array('com_sales', 'cashcount_audit');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->status = 'pending';
		$this->count = array();
	}

	public static function etype() {
		return 'com_sales_cashcount_deposit';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "Deposit $this->guid";
			case 'type':
				return 'deposit';
			case 'types':
				return 'deposits';
			case 'icon':
				return 'picon-list-add';
		}
		return null;
	}

	/**
	 * Delete the cash count deposit.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted deposit {$this->guid}.", 'notice');
		return true;
	}


	/**
	 * Print a form to deposit off of a cash count.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $pines;
		$module = new module('com_sales', 'cashcount/formdeposit', 'content');
		$module->entity = $this;
		return $module;
	}
}