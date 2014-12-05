<?php
/**
 * com_sales_manufacturer class.
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
 * A manufacturer.
 *
 * @package Components\sales
 */
class com_sales_manufacturer extends Entity {
	const etype = 'com_sales_manufacturer';
	protected $tags = array('com_sales', 'manufacturer');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->address_type = 'us';
	}

	/**
	 * Return the entity helper module.
	 * @return module Entity helper module.
	 */
	public function helper() {
		return new module('com_sales', 'manufacturer/helper');
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'manufacturer';
			case 'types':
				return 'manufacturers';
			case 'url_edit':
				if (gatekeeper('com_sales/editmanufacturer'))
					return pines_url('com_sales', 'manufacturer/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_sales/listmanufacturers'))
					return pines_url('com_sales', 'manufacturer/list');
				break;
			case 'icon':
				return 'picon-applications-engineering';
		}
		return null;
	}

	/**
	 * Delete the manufacturer.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted manufacturer $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the manufacturer.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Get the location of the company logo.
	 * @param bool $full Whether to return a full URL (as opposed to relative).
	 * @return string The location of the company logo.
	 */
	public function get_logo($full = false) {
		global $_;
		if (isset($this->logo))
			return $full ? $_->uploader->url($_->uploader->real($this->logo), true) : $this->logo;
		return '';
	}

	/**
	 * Print a form to edit the manufacturer.
	 * @return module The form's module.
	 */
	public function print_form() {
		$module = new module('com_sales', 'manufacturer/form', 'content');
		$module->entity = $this;

		return $module;
	}
}