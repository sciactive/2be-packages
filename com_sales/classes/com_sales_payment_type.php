<?php
/**
 * com_sales_payment_type class.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A payment type.
 *
 * @package Components\sales
 */
class com_sales_payment_type extends Entity {
	const etype = 'com_sales_payment_type';
	protected $tags = array('com_sales', 'payment_type');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->enabled = true;
		$this->processing_type = 'com_sales/instant';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'payment type';
			case 'types':
				return 'payment types';
			case 'url_edit':
				if (gatekeeper('com_sales/editpaymenttype'))
					return pines_url('com_sales', 'paymenttype/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_sales/listpaymenttypes'))
					return pines_url('com_sales', 'paymenttype/list');
				break;
			case 'icon':
				return 'picon-view-bank';
		}
		return null;
	}

	/**
	 * Delete the payment type.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted payment type $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the payment type.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the payment type.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;
		$module = new module('com_sales', 'paymenttype/form', 'content');
		$module->entity = $this;
		$module->processing_types = (array) $_->config->com_sales->processing_types;

		return $module;
	}
}