<?php
/**
 * com_sales_po class.
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
 * A PO.
 *
 * @package Components\sales
 */
class com_sales_po extends Entity {
	const etype = 'com_sales_po';
	protected $tags = array('com_sales', 'po');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->products = array();
		$this->finished = false;
		$this->destination = $_SESSION['user']->group;
		$this->ac->other = 2;
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "PO $this->po_number";
			case 'type':
				return 'PO';
			case 'types':
				return 'POs';
			case 'url_edit':
				if (gatekeeper('com_sales/editpo'))
					return pines_url('com_sales', 'po/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_sales/listpos'))
					return pines_url('com_sales', 'po/list');
				break;
			case 'icon':
				return 'picon-resource-calendar-child';
		}
		return null;
	}

	/**
	 * Return the entity helper module.
	 * @return module Entity helper module.
	 */
	public function helper() {
		return new module('com_sales', 'po/helper');
	}

	/**
	 * Delete the PO.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		// Don't delete the PO if it has received items.
		if (!empty($this->received))
			return false;
		if (!parent::delete())
			return false;
		pines_log("Deleted PO $this->po_number.", 'notice');
		return true;
	}

	/**
	 * Save the PO.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->po_number) || !$this->products)
			return false;
		if (!$this->finished) {
			$this->pending = array();
			$this->pending_products = array();
			foreach ($this->products as &$cur_product) {
				$cur_product['received'] = 0;
				// Count how many of this product has been received.
				foreach ((array) $this->received as $cur_received_stock_entity) {
					if (isset($cur_received_stock_entity) && $cur_product['entity']->is($cur_received_stock_entity->product))
						$cur_product['received']++;
				}
				// If we've received all of them, move on.
				if ($cur_product['received'] >= $cur_product['quantity'])
					continue;
				$cur_pending = $cur_product;
				$cur_pending['quantity'] -= $cur_product['received'];
				$this->pending[] = $cur_pending;
				$this->pending_products[] = $cur_product['entity'];
			}
			unset($cur_product);
			if (empty($this->pending_products))
				$this->finished = true;
		}
		return parent::save();
	}

	/**
	 * Email a notification to the destination.
	 * 
	 * @return bool True on success, false on failure. 
	 */
	public function email() {
		global $_;
		if (empty($this->destination->email))
			return false;
		$module = new module('com_sales', 'po/products_email');
		$module->entity = $this;

		$tracking_links = array();
		if (isset($this->shipper->guid) && $this->shipper->can_track()) {
			foreach ($this->tracking_numbers as $cur_number) {
				$url = h($this->shipper->tracking_url($cur_number));
				$tracking_links[] = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
			}
		} else {
			foreach ($this->tracking_numbers as $cur_number)
				$tracking_links[] = h($cur_number);
		}
		$address = '';
		if ($this->destination->address_type == 'us') {
			if (!empty($this->destination->address_1)) {
				$address .= h($this->destination->address_1.' '.$this->destination->address_2).'<br />';
				$address .= h($this->destination->city).', '.h($this->destination->state).' '.h($this->destination->zip);
			}
		} else
			$address .= str_replace("\n", '<br />', h($this->destination->address_international));

		$macros = array(
			'products' => $module->render(),
			'po_number' => h($this->po_number),
			'ref_number' => h($this->reference_number),
			'vendor' => h($this->vendor->name),
			'destination' => h($this->destination->name),
			'shipper' => h($this->shipper->name),
			'tracking_link' => implode('<br />', $tracking_links),
			'eta' => h($this->eta ? format_date($this->eta, 'date_long') : ''),
			'address' => $address,
			'comments' => h($this->comments),
		);
		return $_->com_mailer->send_mail('com_sales/po_committed', $macros, $this->destination);
	}

	/**
	 * Print a form to edit the PO.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;
		$module = new module('com_sales', 'po/form', 'content');
		$module->entity = $this;
		$module->locations = (array) $_->user_manager->get_groups();
		$module->shippers = (array) $_->nymph->getEntities(array('class' => com_sales_shipper), array('&', 'tag' => array('com_sales', 'shipper')));
		$module->vendors = (array) $_->nymph->getEntities(array('class' => com_sales_vendor), array('&', 'tag' => array('com_sales', 'vendor')));

		return $module;
	}
}