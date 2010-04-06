<?php
/**
 * Save changes to a product.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( isset($_REQUEST['id']) ) {
	if ( !gatekeeper('com_sales/editproduct') )
		punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'listproducts'));
	$product = com_sales_product::factory((int) $_REQUEST['id']);
	if (is_null($product->guid)) {
		pines_error('Requested product id is not accessible');
		return;
	}
} else {
	if ( !gatekeeper('com_sales/newproduct') )
		punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'listproducts'));
	$product = com_sales_product::factory();
}

// General
$product->name = $_REQUEST['name'];
$product->enabled = ($_REQUEST['enabled'] == 'ON');
$product->sku = $_REQUEST['sku'];
$product->description = $_REQUEST['description'];
$product->short_description = $_REQUEST['short_description'];
$product->manufacturer = ($_REQUEST['manufacturer'] == 'null' ? null : com_sales_manufacturer::factory((int) $_REQUEST['manufacturer']));
if (is_null($product->manufacturer->guid))
	$product->manufacturer = null;
$product->manufacturer_sku = $_REQUEST['manufacturer_sku'];

// Purchasing
$product->stock_type = $_REQUEST['stock_type'];
$product->vendors = (array) json_decode($_REQUEST['vendors']);
foreach ($product->vendors as &$cur_vendor) {
	$cur_vendor = array(
		'entity' => new com_sales_vendor(intval($cur_vendor->key)),
		'sku' => $cur_vendor->values[1],
		'cost' => $cur_vendor->values[2]
	);
	if (is_null($cur_vendor['entity']->guid))
		$cur_vendor['entity'] = null;
}
unset($cur_vendor);

// Pricing
$product->pricing_method = $_REQUEST['pricing_method'];
$product->unit_price = floatval($_REQUEST['unit_price']);
$product->margin = floatval($_REQUEST['margin']);
$product->floor = floatval($_REQUEST['floor']);
$product->ceiling = floatval($_REQUEST['ceiling']);
// TODO: Tax exempt by location.
$product->tax_exempt = ($_REQUEST['tax_exempt'] == 'ON');
$product->additional_tax_fees = array();
if (is_array($_REQUEST['additional_tax_fees'])) {
	foreach ($_REQUEST['additional_tax_fees'] as $cur_tax_fee_guid) {
		$cur_tax_fee = com_sales_tax_fee::factory((int) $cur_tax_fee_guid);
		if (isset($cur_tax_fee->guid))
			$product->additional_tax_fees[] = $cur_tax_fee;
	}
}

// Attributes
$product->weight = floatval($_REQUEST['weight']);
$product->rma_after = floatval($_REQUEST['rma_after']);
$product->serialized = ($_REQUEST['serialized'] == 'ON');
$product->discountable = ($_REQUEST['discountable'] == 'ON');
$product->require_customer = ($_REQUEST['require_customer'] == 'ON');
$product->one_per_ticket = ($_REQUEST['one_per_ticket'] == 'ON');
$product->hide_on_invoice = ($_REQUEST['hide_on_invoice'] == 'ON');
$product->non_refundable = ($_REQUEST['non_refundable'] == 'ON');
$product->additional_barcodes = explode(',', $_REQUEST['additional_barcodes']);
$product->actions = (array) $_REQUEST['actions'];

if (empty($product->name)) {
	$product->print_form();
	pines_notice('Please specify a name.');
	return;
}
if ($product->stock_type == 'non_stocked' && $product->pricing_method == 'margin') {
	$product->print_form();
	pines_notice('Margin pricing is not available for non stocked items.');
	return;
}
$test = $pines->entity_manager->get_entity(array('data' => array('name' => $product->name), 'tags' => array('com_sales', 'product'), 'class' => com_sales_product));
if (isset($test) && $test->guid != $_REQUEST['id']) {
	$product->print_form();
	pines_notice('There is already a product with that name. Please choose a different name.');
	return;
}

if ($pines->config->com_sales->global_products)
	$product->ac->other = 1;

if ($product->save()) {
	pines_notice('Saved product ['.$product->name.']');
	// Assign the product to the selected categories.
	// We have to do this here, because new products won't have a GUID until now.
	$categories = (array) json_decode($_REQUEST['categories']);
	array_map('intval', $categories);
	$all_categories = $pines->com_sales->get_category_array();
	foreach($all_categories as $cur_cat) {
		if (!is_array($cur_cat->products))
			$cur_cat->products = array();

		if (in_array($cur_cat->guid, $categories) && !in_array($product->guid, $cur_cat->products)) {
			$cur_cat->products[] = $product->guid;
			if (!$cur_cat->save())
				pines_error('Failed to add product to category: '.$cur_cat->name);
		} elseif (!in_array($cur_cat->guid, $categories) && in_array($product->guid, $cur_cat->products)) {
			$cur_cat->products = array_diff($cur_cat->products, array($product->guid));
			if (!$cur_cat->save())
				pines_error('Failed to remove product from category: '.$cur_cat->name);
		}
	}
} else {
	pines_error('Error saving product. Do you have permission?');
}

$pines->com_sales->list_products();
?>