<?php
/**
 * Search products, returning JSON.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/searchproducts'))
	punt_user(null, pines_url('com_sales', 'product/search', $_REQUEST));

$_->page->override = true;
header('Content-Type: application/json');

$query = trim($_REQUEST['q']);
$r_query = '/'.str_replace(' ', '.*', preg_quote($query)).'/i';

// Get Product Entities.
if ($query == '*') {
	$products = $_->entity_manager->get_entities(
			array('class' => com_sales_product),
			array('&',
				'tag' => array('com_sales', 'product'),
				'strict' => array('enabled', $_REQUEST['enabled'] == 'true')
			)
		);
} else {
	$products = $_->entity_manager->get_entities(
			array('class' => com_sales_product),
			array('&',
				'tag' => array('com_sales', 'product'),
				'strict' => array('enabled', $_REQUEST['enabled'] == 'true')
			),
			array('|',
				'match' => array(
					array('name', $r_query), 
					array('sku', $r_query)
				)
			)
		);

	// Also check categories
	$categories = $_->entity_manager->get_entities(
			array('class' => com_sales_category),
			array('&',
				'tag' => array('com_sales', 'category'),
				'strict' => array('enabled', true),
				'match' => array('name', $r_query)
			)
		);
	foreach ($categories as $cur_category) {
		foreach ($cur_category->products as $cur_product) {
			if ($cur_product && $cur_product->enabled == ($_REQUEST['enabled'] == 'true') && !$cur_product->in_array($products))
				$products[] = $cur_product;
		}
	}
}

foreach ($products as $key => &$product) {
	$vendors = array();
	foreach((array) $product->vendors as $cur_vendor) {
		$vendors[] = (object) array(
			'guid' => $cur_vendor['entity']->guid,
			'name' => (string) $cur_vendor['entity']->name,
			'sku' => $cur_vendor['sku'],
			'cost' => '$'.$_->com_sales->round($cur_vendor['cost'], true),
			'link' => $cur_vendor['link']
		);
	}
	switch ($product->stock_type) {
		case 'non_stocked':
			$stock_type = 'Non Stocked';
			break;
		case 'stock_optional':
			$stock_type = 'Stock Optional';
			break;
		case 'regular_stock':
			$stock_type = 'Regular Stock';
			break;
		default:
			$stock_type = 'Unrecognized';
			break;
	} 
	$additional_barcodes = implode(', ', $product->additional_barcodes);
	$serialized = (bool) $product->serialized;
	$discountable = (bool) $product->discountable;
	$show_in_shop = (bool) $product->show_in_shop;
	$featured = (bool) $product->featured;
	$custom_item = (bool) $product->custom_item;
	$receipt_description = (!empty($product->receipt_description) ? $product->receipt_description : '');
	
	$images = array();
	if (isset($product->thumbnail)) {
		if (file_exists($_->uploader->real($product->thumbnail)))
			$images[] = 'Thumbnail';
		else
			$images[] = 'Thumbnail - Broken';
	}
	$image_desc = array();
	foreach ((array) $product->images as $cur_image) {
		$image_desc[0] = 'Images';
		if (empty($cur_image['alt']))
			$image_desc[1] = 'Missing Desc';
		if (!file_exists($_->uploader->real($cur_image['file'])))
			$image_desc[2] = 'Broken';
	}
	if ($image_desc)
		$images[] = implode(' - ', $image_desc);
	if ($images)
		$product_images = implode(', ', $images);
	else
		$product_images = 'None';
	
	$created = format_date($product->p_cdate, 'date_short');
	$modified = format_date($product->p_mdate, 'date_short');
	$expiration = (!empty($product->product_exp)) ? format_date($product->product_exp, 'date_short') : '';
	
	$json_struct = (object) array(
		'guid' => $product->guid,
		'sku' => $product->sku,
		'name' => $product->name,
		'price' => $_->com_sales->round($product->unit_price, true),
		'vendors' => $vendors,
		'manufacturer_guid' => $product->manufacturer->guid,
		'manufacturer_name' => $product->manufacturer->name,
		'manufacturer_sku' => $product->manufacturer_sku,
		'stock_type' => $stock_type,
		'custom_item' => $custom_item,
		'serialized' => $serialized,
		'discountable' => $discountable,
		'additional_barcodes' => $additional_barcodes,
		'images' => $product_images,
		'receipt_description' => $receipt_description,
		'shop' => $show_in_shop,
		'featured' => $featured,
		'created' => $created,
		'modified' => $modified,
		'expiration' => $expiration,
	);
	$product = $json_struct;
}
unset($product);

if (!$products)
	$products = null;

$_->page->override_doc(json_encode($products));