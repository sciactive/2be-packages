<?php
/**
 * com_shop_shop class.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A shop.
 *
 * @package Components\shop
 */
class com_shop_shop extends Entity {
	const etype = 'com_shop_shop';
	protected $tags = array('com_shop', 'shop');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->enabled = true;
		$this->attributes = array();
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'shop';
			case 'types':
				return 'shops';
			case 'url_view':
				return pines_url('com_shop', null, array('id' => $this->guid));
				break;
			case 'url_edit':
				if (gatekeeper('com_shop/editshop'))
					return pines_url('com_shop', 'shop/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_shop/listshops'))
					return pines_url('com_shop', 'shop/list');
				break;
			case 'icon':
				return 'picon-view-pim-journal';
		}
		return null;
	}

	/**
	 * Delete the shop.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted shop $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the shop.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Get the products that belong to this shop.
	 *
	 * @param bool $only_enabled Only retrieve enabled products.
	 * @param bool $only_show_in_shop Only retrieve products set to show in the shop.
	 * @return array The resulting products.
	 */
	public function get_products($only_enabled = true, $only_show_in_shop = true) {
		global $_;
		$selector = array('&',
			'tag' => array('com_sales', 'product'),
			'ref' => array('shop', $this)
		);
		if ($only_enabled || $only_show_in_shop)
			$selector['data'] = array();
		if ($only_enabled)
			$selector['data'][] = array('enabled', true);
		if ($only_show_in_shop)
			$selector['data'][] = array('show_in_shop', true);
		return $_->nymph->getEntities(
				array('class' => com_sales_product),
				$selector
			);
	}

	/**
	 * View the shop's home.
	 * @return module The module.
	 */
	public function home() {
		$module = new module('com_shop', 'shop/home', 'content');
		$module->entity = $this;

		return $module;
	}

	/**
	 * Print a shop header.
	 * @return module The module.
	 */
	public function print_header() {
		$module = new module('com_shop', 'shop/header', '');
		$module->entity = $this;
		$module->detach();
		echo $module->render();

		return $module;
	}

	/**
	 * Print a shop owner info.
	 * @return module The module.
	 */
	public function print_owner() {
		$module = new module('com_shop', 'shop/owner', '');
		$module->entity = $this;
		$module->detach();
		echo $module->render();

		return $module;
	}

	/**
	 * Print a shop's printuct featurette.
	 * @return module The module.
	 */
	public function print_product_featurette() {
		$module = new module('com_shop', 'products/list', '');
		$module->products = array_slice(array_reverse($this->get_products()), 0, 5);
		$module->no_controls = true;
		$module->template = 'featurette';
		$module->detach();
		echo $module->render();

		return $module;
	}

	/**
	 * Print a form to edit the shop.
	 * @return module The form's module.
	 */
	public function print_form() {
		$module = new module('com_shop', 'shop/form', 'content');
		$module->entity = $this;

		return $module;
	}
}