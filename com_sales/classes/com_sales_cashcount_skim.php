<?php
/**
 * com_sales_cashcount_skim class.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A skim off of a cash count.
 *
 * @package Components\sales
 */
class com_sales_cashcount_skim extends entity {
	protected $tags = array('com_sales', 'cashcount_audit');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->status = 'pending';
		$this->count = array();
	}

	public static function etype() {
		return 'com_sales_cashcount_skim';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "Skim $this->guid";
			case 'type':
				return 'skim';
			case 'types':
				return 'skims';
			case 'icon':
				return 'picon-list-remove';
		}
		return null;
	}

	/**
	 * Delete the cash count skim.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted skim {$this->guid}.", 'notice');
		return true;
	}


	/**
	 * Print a form to skim off of a cash count.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;
		$module = new module('com_sales', 'cashcount/formskim', 'content');
		$module->entity = $this;
		return $module;
	}
}