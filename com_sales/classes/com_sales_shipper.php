<?php
/**
 * com_sales_shipper class.
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
 * A shipper.
 *
 * @package Components\sales
 */
class com_sales_shipper extends entity {
	protected $tags = array('com_sales', 'shipper');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->address_type = 'us';
		$this->tracking = 'custom';
	}

	public static function etype() {
		return 'com_sales_shipper';
	}

	/**
	 * Return the entity helper module.
	 * @return module Entity helper module.
	 */
	public function helper() {
		return new module('com_sales', 'shipper/helper');
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'shipper';
			case 'types':
				return 'shippers';
			case 'url_edit':
				if (gatekeeper('com_sales/editshipper'))
					return pines_url('com_sales', 'shipper/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_sales/listshippers'))
					return pines_url('com_sales', 'shipper/list');
				break;
			case 'icon':
				return 'picon-mail-folder-outbox';
		}
		return null;
	}

	/**
	 * Delete the shipper.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted shipper $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the shipper.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the shipper.
	 * @return module The form's module.
	 */
	public function print_form() {
		$module = new module('com_sales', 'shipper/form', 'content');
		$module->entity = $this;

		return $module;
	}

	/**
	 * Get the tracking URL.
	 * @param string $number The tracking number.
	 * @return string The tracking URL.
	 */
	public function tracking_url($number) {
		if ($this->tracking == 'custom') {
			if (!empty($this->tracking_url))
				return str_replace('#tracking_number#', urlencode($number), $this->tracking_url);
			else
				return $number;
		}
		global $_;
		return str_replace('#tracking_number#', urlencode($number), $_->com_sales->tracking_urls[$this->tracking]);
	}

	/**
	 * Determine if this shipper has tracking set up.
	 * @return bool Whether this shipper supports tracking.
	 */
	public function can_track() {
		return (isset($this->tracking) && ($this->tracking != 'custom' || ($this->tracking == 'custom' && !empty($this->tracking_url) && strpos($this->tracking_url, '#tracking_number#') !== false)));
	}
}