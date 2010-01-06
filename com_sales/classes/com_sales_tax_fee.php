<?php
/**
 * com_sales_tax_fee class.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A tax/fee.
 *
 * @package Pines
 * @subpackage com_sales
 */
class com_sales_tax_fee extends entity {
	/**
	 * Load a tax/fee.
	 * @param int $id The ID of the tax/fee to load, null for a new tax/fee.
	 */
	public function __construct($id = null) {
		parent::__construct();
		$this->add_tag('com_sales', 'tax_fee');
		if (!is_null($id)) {
			global $config;
			$entity = $config->entity_manager->get_entity($id, $this->tags, get_class($this));
			if (is_null($entity))
				return;
			$this->guid = $entity->guid;
			$this->parent = $entity->parent;
			$this->tags = $entity->tags;
			$this->entity_cache = array();
			$this->put_data($entity->get_data());
		}
	}

	/**
	 * Create a new instance.
	 */
	public static function factory() {
		global $config;
		$class = get_class();
		$args = func_get_args();
		$entity = new $class($args[0]);
		$config->hook->hook_object($entity, $class.'->', false);
		return $entity;
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
		$module = new module('com_sales', 'form_tax_fee', 'content');
		$module->entity = $this;
		$module->locations = $config->user_manager->get_group_array();

		return $module;
	}
}

?>