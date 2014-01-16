<?php
/**
 * com_sales_tax_fee class.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A tax/fee.
 *
 * @package Components\sales
 */
class com_sales_tax_fee extends entity {
	/**
	 * Load a tax/fee.
	 * @param int $id The ID of the tax/fee to load, 0 for a new tax/fee.
	 */
	public function __construct($id = 0) {
		parent::__construct();
		$this->add_tag('com_sales', 'tax_fee');
		if ($id > 0) {
			global $pines;
			$entity = $pines->entity_manager->get_entity(array('class' => get_class($this)), array('&', 'guid' => $id, 'tag' => $this->tags));
			if (isset($entity)) {
				$this->guid = $entity->guid;
				$this->tags = $entity->tags;
				$this->put_data($entity->get_data(), $entity->get_sdata());
				return;
			}
		}
		// Defaults.
		$this->enabled = true;
		$this->locations = array();
	}

	/**
	 * Create a new instance.
	 * @return com_sales_tax_fee The new instance.
	 */
	public static function factory() {
		global $pines;
		$class = get_class();
		$args = func_get_args();
		$entity = new $class($args[0]);
		$pines->hook->hook_object($entity, $class.'->', false);
		return $entity;
	}

	public static function etype() {
		return 'com_sales_tax_fee';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'tax/fee';
			case 'types':
				return 'taxes/fees';
			case 'url_edit':
				if (gatekeeper('com_sales/edittaxfee'))
					return pines_url('com_sales', 'taxfee/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_sales/listtaxfees'))
					return pines_url('com_sales', 'taxfee/list');
				break;
			case 'icon':
				return 'picon-office-chart-pie';
		}
		return null;
	}

	/**
	 * Delete the tax/fee.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted tax/fee $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the tax/fee.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the tax/fee.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $pines;
		$module = new module('com_sales', 'taxfee/form', 'content');
		$module->entity = $this;
		$module->locations = $pines->user_manager->get_groups();

		return $module;
	}
}