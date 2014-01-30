<?php
/**
 * com_customer_company class.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A company.
 *
 * @package Components\customer
 */
class com_customer_company extends entity {
	protected $tags = array('com_customer', 'company');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->address_type = 'us';
	}

	public static function etype() {
		return 'com_customer_company';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'company';
			case 'types':
				return 'companies';
			case 'url_edit':
				if (gatekeeper('com_customer/editcompany'))
					return pines_url('com_customer', 'company/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_customer/listcompanies'))
					return pines_url('com_customer', 'company/list');
				break;
			case 'icon':
				return 'picon-resource-group';
		}
		return null;
	}

	/**
	 * Delete the company.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted company $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the company.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the company.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $pines;
		$module = new module('com_customer', 'company/form', 'content');
		$module->entity = $this;

		return $module;
	}
}