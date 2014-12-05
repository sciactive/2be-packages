<?php
/**
 * com_sales_cashcount_audit class.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * An audit of a cash count.
 *
 * @package Components\sales
 */
class com_sales_cashcount_audit extends Entity {
	const etype = 'com_sales_cashcount_audit';
	protected $tags = array('com_sales', 'cashcount_audit');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->status = 'pending';
		$this->count = array();
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "Audit $this->guid";
			case 'type':
				return 'audit';
			case 'types':
				return 'audits';
			case 'icon':
				return 'picon-document-edit-verify';
		}
		return null;
	}

	/**
	 * Delete the cash count audit.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted audit {$this->guid}.", 'notice');
		return true;
	}


	/**
	 * Print a form to audit a cash count.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;
		$module = new module('com_sales', 'cashcount/formaudit', 'content');
		$module->entity = $this;
		return $module;
	}
}