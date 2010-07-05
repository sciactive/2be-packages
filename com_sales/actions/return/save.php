<?php
/**
 * Save changes to a return.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( isset($_REQUEST['id']) ) {
	if ( !gatekeeper('com_sales/editreturn') )
		punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'return/list'));
	$return = com_sales_return::factory((int) $_REQUEST['id']);
	if (!isset($return->guid)) {
		pines_error('Requested return id is not accessible.');
		return;
	}
} elseif ( isset($_REQUEST['sale_id']) ) {
	if ( !gatekeeper('com_sales/newreturnwsale') )
		punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'return/list'));
	$sale = com_sales_sale::factory((int) $_REQUEST['sale_id']);
	if (!isset($sale->guid)) {
		pines_error('Requested sale id is not accessible.');
		return;
	}
	$return = com_sales_return::factory();
	$return->attach_sale($sale);
} else {
	if ( !gatekeeper('com_sales/newreturn') )
		punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'return/list'));
	$return = com_sales_return::factory();
}

if ($pines->config->com_sales->com_customer && $return->status != 'processed' && $return->status != 'voided' && !isset($return->sale->guid)) {
	$return->customer = null;
	if (preg_match('/^\d+/', $_REQUEST['customer'])) {
		$return->customer = com_customer_customer::factory((int) $_REQUEST['customer']);
		if (!isset($return->customer->guid))
			$return->customer = null;
	}
}
// Used for product error checking.
$product_error = false;
if ($return->status != 'processed' && $return->status != 'voided') {
	$return->products = (array) json_decode($_REQUEST['products']);
	if (empty($return->products)) {
		pines_notice('No products were selected.');
		$product_error = true;
	} else {
		// Grab the products from the sale, if there is one.
		if (isset($return->sale))
			$old_products = (array) $return->get_sale_products();
		foreach ($return->products as $key => &$cur_product) {
			$cur_product_entity = com_sales_product::factory((int) $cur_product->key);
			$cur_sku = $cur_product_entity->sku;
			$cur_serial = $cur_product->values[2];
			$cur_qty = (int) $cur_product->values[4];
			$cur_price = (float) $cur_product->values[5];
			$cur_discount = $cur_product->values[6];
			if (!isset($cur_product_entity->guid)) {
				pines_error("Product with id [$cur_product->key] and entered SKU [$cur_sku] was not found.");
				unset($return->products[$key]);
				$product_error = true;
				continue;
			}
			if (!$cur_product_entity->enabled) {
				pines_error("Product with id [$cur_product->key] is not enabled.");
				unset($return->products[$key]);
				$product_error = true;
				continue;
			}
			if ($cur_product_entity->serialized)
				$cur_qty = 1;
			if ($cur_product_entity->serialized && empty($cur_serial)) {
				pines_notice("Product with SKU [$cur_sku] requires a serial.");
				$product_error = true;
			}
			if ($cur_qty < 1) {
				pines_notice("Product with SKU [$cur_sku] has a zero or negative quantity.");
				$product_error = true;
			}
			if ($cur_product_entity->pricing_method != 'variable' && $cur_product_entity->unit_price != $cur_price) {
				pines_notice("Product with SKU [$cur_sku] has an incorrect price.");
				$product_error = true;
			}
			if (!$cur_product_entity->discountable && !empty($cur_discount)) {
				pines_notice("Product with SKU [$cur_sku] is not discountable.");
				$product_error = true;
			}
			if (isset($return->sale)) {
				// Search through the products from the sale and find the entry that matches.
				// TODO: Will this cause problems with matching items that have different quantities?
				// Like widget x 5 + widget x 1. Remove first line, and it may match first line anyway.
				// Is that a problem? It only happens with unserialized items.
				$found = false;
				foreach ($old_products as $old_key => $cur_old_product) {
					if ($cur_product_entity->is($cur_old_product['entity']) && $cur_sku == $cur_old_product['sku'] && $cur_serial == $cur_old_product['serial'] && $cur_qty <= $cur_old_product['quantity']) {
						$cur_product = $cur_old_product;
						$cur_product['quantity'] = $cur_qty;
						$cur_product['price'] = $cur_price;
						$cur_product['discount'] = $cur_discount;
						unset($old_products[$old_key]);
						$found = true;
						break;
					}
				}
				if (!$found) {
					pines_notice("Product with SKU [$cur_sku] was not found on original sale. Has it already been returned?");
					$product_error = true;
				}
			} else {
				$cur_product = array(
					'entity' => $cur_product_entity,
					'sku' => $cur_sku,
					'serial' => $cur_serial,
					'quantity' => $cur_qty,
					'price' => $cur_price,
					'discount' => $cur_discount
				);
			}
		}
		unset($cur_product);
	}
}
// Used for payment error checking.
$payment_error = false;
if ($return->status != 'processed' && $return->status != 'voided') {
	foreach ($return->payments as $key => $cur_payment) {
		if (!in_array($cur_payment['status'], array('approved', 'declined', 'tendered')))
			unset($return->payments[$key]);
	}
	$payments = (array) json_decode($_REQUEST['payments']);
	foreach ($payments as $cur_payment) {
		$cur_payment_type_entity = com_sales_payment_type::factory((int) $cur_payment->key);
		// Not used, but possibly in the future for logging purposes. (If the type is deleted.)
		$cur_type = $cur_payment->values[0];
		$cur_amount = (float) $cur_payment->values[1];
		$cur_status = $cur_payment->values[2];
		$data = $cur_payment->data;
		if (in_array($cur_status, array('approved', 'declined', 'tendered')))
			continue;
		if (!isset($cur_payment_type_entity->guid)) {
			pines_error("Payment type with id [$cur_payment->key] was not found.");
			$payment_error = true;
			continue;
		}
		if (!$cur_payment_type_entity->enabled) {
			pines_error("Payment type with id [$cur_payment->key] is not enabled.");
			$payment_error = true;
			continue;
		}
		if ($cur_amount <= 0) {
			pines_notice('A payment was entered without an amount.');
			$payment_error = true;
		}
		$data_array = array();
		if (is_array($data->data)) {
			foreach ($data->data as $cur_data) {
				$data_array[$cur_data->name] = $cur_data->value;
			}
		}
		$return->payments[] = array(
			'entity' => $cur_payment_type_entity,
			'type' => $cur_type,
			'amount' => $cur_amount,
			'status' => $cur_status,
			'data' => $data_array
		);
	}
}
$return->comments = $_REQUEST['comment_saver'];

if ($product_error || $payment_error) {
	$return->print_form();
	return;
}

if ($pines->config->com_sales->global_returns)
	$return->ac->other = 1;

if ($_REQUEST['process'] == 'process' && $return->status != 'processed' && $return->status != 'voided') {
	if (!$return->complete()) {
		$return->print_form();
		pines_error('There was an error while completing the return. Please check that all information is correct and resubmit.');
		return;
	}
}

if (!isset($return->status) || $return->status == 'quoted') {
	$return->status = 'quoted';
	$return->total();
}

if ($return->save()) {
	pines_notice('Saved return ['.$return->id.']');
	redirect(pines_url('com_sales', 'return/receipt', array('id' => $return->guid)));
} else {
	$return->print_form();
	pines_error('Error saving return. Do you have permission?');
	return;
}

?>