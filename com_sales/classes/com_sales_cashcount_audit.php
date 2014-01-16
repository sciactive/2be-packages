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
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * An audit of a cash count.
 *
 * @package Components\sales
 */
class com_sales_cashcount_audit extends entity {
	/**
	 * Load a cash count.
	 * @param int $id The ID of the cashcount to load, 0 for a new cashcount.
	 */
	public function __construct($id = 0) {
		parent::__construct();
		$this->add_tag('com_sales', 'cashcount_audit');
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
		// Defaults
		$this->status = 'pending';
		$this->count = array();
	}

	/**
	 * Create a new instance.
	 * @return com_sales_cashcount_audit The new instance.
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
		return 'com_sales_cashcount_audit';
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
		global $pines;
		$module = new module('com_sales', 'cashcount/formaudit', 'content');
		$module->entity = $this;
		return $module;
	}
}