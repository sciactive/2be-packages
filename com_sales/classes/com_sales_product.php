<?php
/**
 * com_sales_product class.
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
 * A product.
 *
 * @package Components\sales
 */
class com_sales_product extends entity {
	protected $tags = array('com_sales', 'product');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->enabled = true;
		$this->images = array();
		$this->additional_tax_fees = array();
		$this->return_checklists = array();
		$this->serialized = true;
		$this->discountable = true;
		$this->require_customer = true;
		$this->additional_barcodes = array();
		$this->actions = array();
		$this->show_in_storefront = false;
		$this->title_use_name = true;
		$this->title_position = 'prepend';
		$this->meta_tags = array();
	}

	public static function etype() {
		return 'com_sales_product';
	}

	public function info($type) {
		global $_;
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'product';
			case 'types':
				return 'products';
			case 'url_view':
				if ($_->config->com_sales->com_storefront && $this->show_in_storefront)
					return pines_url('com_storefront', 'product', array('a' => $this->alias));
				break;
			case 'url_edit':
				if (gatekeeper('com_sales/editproduct'))
					return pines_url('com_sales', 'product/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_sales/listproducts'))
					return pines_url('com_sales', 'product/list');
				break;
			case 'icon':
				return 'picon-view-barcode';
		}
		return null;
	}

	/**
	 * Delete the product.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		global $_;
		// Remove product from categories.
		$cats = $_->entity_manager->get_entities(
				array('class' => com_sales_category, 'skip_ac' => true),
				array('&',
					'tag' => array('com_sales', 'category'),
					'ref' => array('products', $this)
				)
			);
		foreach ($cats as &$cur_cat) {
			while (($key = $this->array_search($cur_cat->products)) !== false) {
				unset($cur_cat->products[$key]);
				$cur_cat->products = array_values($cur_cat->products);
			}
			if (!$cur_cat->save()) {
				pines_error("Couldn't remove product from category, {$cur_cat->name}.");
				pines_log("Couldn't remove product from category, {$cur_cat->name}.", 'error');
				return false;
			}
		}
		unset($cur_cat);
		if (!parent::delete())
			return false;
		pines_log("Deleted product $this->name.", 'notice');
		return true;
	}

	/**
	 * Get an array of categories' GUIDs this product belongs to.
	 * @return array An array of GUIDs.
	 */
	public function get_categories_guid() {
		$categories = $this->get_categories($product);
		foreach ($categories as &$cur_cat) {
			$cur_cat = $cur_cat->guid;
		}
		unset($cur_cat);
		return $categories;
	}

	/**
	 * Get an array of categories this product belongs to.
	 * @return array An array of categories.
	 */
	public function get_categories() {
		global $_;
		$categories = (array) $_->entity_manager->get_entities(array('class' => com_sales_category), array('&', 'tag' => array('com_sales', 'category'), 'ref' => array('products', $this)));
		return $categories;
	}

	/**
	 * Save the product.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the product.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;
		$module = new module('com_sales', 'product/form', 'content');
		$module->entity = $this;
		$module->categories = (array) $_->entity_manager->get_entities(
				array('class' => com_sales_category),
				array('&',
					'tag' => array('com_sales', 'category'),
					'data' => array('enabled', true)
				)
			);
		$module->manufacturers = (array) $_->entity_manager->get_entities(array('class' => com_sales_manufacturer), array('&', 'tag' => array('com_sales', 'manufacturer')));
		$_->entity_manager->sort($module->manufacturers, 'name');
		$module->vendors = (array) $_->entity_manager->get_entities(array('class' => com_sales_vendor), array('&', 'tag' => array('com_sales', 'vendor')));
		$module->tax_fees = (array) $_->entity_manager->get_entities(
				array('class' => com_sales_tax_fee),
				array('&',
					'tag' => array('com_sales', 'tax_fee'),
					'data' => array('enabled', true)
				)
			);
		$module->return_checklists = (array) $_->entity_manager->get_entities(
				array('class' => com_sales_return_checklist),
				array('&',
					'tag' => array('com_sales', 'return_checklist'),
					'data' => array('enabled', true)
				)
			);
		$module->actions = (array) $_->config->com_sales->product_actions;
		if ($_->config->com_sales->com_hrm) {
			$module->groups = (array) $_->user_manager->get_groups();
			usort($module->groups, array($this, 'sort_groups'));
		}
		
		return $module;
	}

	/**
	 * Sort groups.
	 * @param group $a Group.
	 * @param group $b Group.
	 * @return bool Group order.
	 */
	private function sort_groups($a, $b) {
		$aname = empty($a->name) ? $a->groupname : $a->name;
		$bname = empty($b->name) ? $b->groupname : $b->name;
		return strtolower($aname) > strtolower($bname);
	}
}