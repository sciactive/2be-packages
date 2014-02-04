<?php
/**
 * Save changes to a product.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( isset($_REQUEST['id']) ) {
	if ( !gatekeeper('com_sales/editproduct') )
		punt_user(null, pines_url('com_sales', 'product/list'));
	$product = com_sales_product::factory((int) $_REQUEST['id']);
	if (!isset($product->guid)) {
		pines_error('Requested product id is not accessible.');
		return;
	}
} else {
	if ( !gatekeeper('com_sales/newproduct') )
		punt_user(null, pines_url('com_sales', 'product/list'));
	$product = com_sales_product::factory();
}

// General
$product->name = $_REQUEST['name'];
$product->enabled = ($_REQUEST['enabled'] == 'ON');
$product->autocomplete_hide = ($_REQUEST['autocomplete_hide'] == 'ON');
$product->sku = $_REQUEST['sku'];
$product->receipt_description = $_REQUEST['receipt_description'];
$product->short_description = $_REQUEST['short_description'];
$product->description = $_REQUEST['description'];
$product->manufacturer = ($_REQUEST['manufacturer'] == 'null' ? null : com_sales_manufacturer::factory((int) $_REQUEST['manufacturer']));
if (!isset($product->manufacturer->guid))
	$product->manufacturer = null;
$product->manufacturer_sku = $_REQUEST['manufacturer_sku'];

// Images
// First remember all images, so we can check if they got removed when we're done.
$prev_images = array();
foreach ((array) $product->images as $cur_image) {
	if (file_exists($cur_image['file']))
		$prev_images[] = $cur_image['file'];
	if (file_exists($cur_image['thumbnail']))
		$prev_images[] = $cur_image['thumbnail'];
}
if (file_exists($product->thumbnail))
	$prev_images[] = $product->thumbnail;

$product->images = (array) json_decode($_REQUEST['images'], true);
if (!isset($product->images_dir))
	$product->images_dir = uniqid();
$dir = $_->config->upload_location.$_->config->com_sales->product_images_directory.$product->images_dir.'/';
foreach ($product->images as $key => &$cur_image) {
	if ($cur_image['source'] == 'temp') {
		$tmp_filename = uniqid();
		// Save the main image.
		$file = $_->uploader->temp($cur_image['file']);
		if ($file) {
			if (!file_exists($file)) {
				unset($product->images[$key]);
				pines_error("Error reading image: {$cur_image['file']}");
				continue;
			}

			$image = new Imagick($file);
			if (!$image) {
				unset($product->images[$key]);
				pines_error("Error opening image: {$cur_image['file']}");
				continue;
			}

			$_->com_sales->process_product_image($image, 'prod_img', $cur_image['options']);

			if (!file_exists($dir)) {
				if (!mkdir($dir, 0755, true)) {
					unset($product->images[$key]);
					pines_error("Error making image directory for product {$product->name}: $dir.");
					continue;
				}
			}

			if (!$image->writeImage("{$dir}{$tmp_filename}.png")) {
				unset($product->images[$key]);
				pines_error("Error saving image: {$cur_image['file']}");
				continue;
			}
			$cur_image['file'] = "{$dir}{$tmp_filename}.png";
		}
		// Now save the thumbnail copy.
		$file = $_->uploader->temp($cur_image['thumbnail']);
		if ($file) {
			if (!file_exists($file)) {
				unset($product->images[$key]);
				pines_error("Error reading image: {$cur_image['thumbnail']}");
				continue;
			}

			$image = new Imagick($file);
			if (!$image) {
				unset($product->images[$key]);
				pines_error("Error opening image: {$cur_image['thumbnail']}");
				continue;
			}

			$_->com_sales->process_product_image($image, 'prod_tmb', $cur_image['options']);

			if (!file_exists($dir)) {
				if (!mkdir($dir, 0755, true)) {
					unset($product->images[$key]);
					pines_error("Error making image directory for product {$product->name}: $dir.");
					continue;
				}
			}

			if (!$image->writeImage("{$dir}{$tmp_filename}_t.png")) {
				unset($product->images[$key]);
				pines_error("Error saving image: {$cur_image['file']}");
				continue;
			}
			$cur_image['thumbnail'] = "{$dir}{$tmp_filename}_t.png";
		}
	} else {
		$cur_image['file'] == clean_filename($cur_image['file']);
		$cur_image['thumbnail'] == clean_filename($cur_image['thumbnail']);
		$do_thumbnail = true;
		// Only process files if the options are set.
		if (is_numeric($cur_image['options']['x']) && is_numeric($cur_image['options']['y']) && is_numeric($cur_image['options']['w']) && is_numeric($cur_image['options']['h'])) {
			// First make the thumbnail image.
			$image = new Imagick($cur_image['file']);
			if (!$image) {
				pines_error("Error opening image for thumbnail: {$cur_image['file']}");
				continue;
			}

			$_->com_sales->process_product_image($image, 'prod_tmb', $cur_image['options']);
			if (!$image->writeImage($cur_image['thumbnail']))
				pines_error("Error saving thumbnail: {$cur_image['thumbnail']}");

			// Now the main image.
			$image = new Imagick($cur_image['file']);
			if (!$image) {
				pines_error("Error opening image: {$cur_image['file']}");
				continue;
			}

			$_->com_sales->process_product_image($image, 'prod_img', $cur_image['options']);
			if (!$image->writeImage($cur_image['file'])) {
				pines_error("Error saving image: {$cur_image['file']}");
				continue;
			}
			// Don't redo the thumbnail cause the main image was cropped.
			$do_thumbnail = false;
		}
		// Changing the thumbnail method must remake the thumbnail from the main image.
		if ($do_thumbnail && $cur_image['options']['tmb_method']) {
			$image = new Imagick($cur_image['file']);
			if (!$image) {
				pines_error("Error opening image for thumbnail: {$cur_image['file']}");
				continue;
			}

			$_->com_sales->process_product_image($image, 'prod_tmb', $cur_image['options']);
			if (!$image->writeImage($cur_image['thumbnail'])) {
				pines_error("Error saving thumbnail: {$cur_image['thumbnail']}");
				continue;
			}
		}
	}
	if (!file_exists($cur_image['file']) && !file_exists($cur_image['thumbnail']))
		unset($product->images[$key]);
}
unset($cur_image);
$product->images = array_values($product->images);
$product->thumbnail = $_REQUEST['thumbnail'];
$file = $_->uploader->temp($product->thumbnail);
while (true) {
	if ($file) {
		if (!file_exists($file)) {
			unset($product->thumbnail);
			pines_error("Error reading image: {$product->thumbnail}");
			break;
		}

		$image = new Imagick($file);
		if (!$image) {
			unset($product->thumbnail);
			pines_error("Error opening image: {$product->thumbnail}");
			break;
		}

		$_->com_sales->process_product_image($image, 'thumbnail');

		if (!file_exists($dir)) {
			if (!mkdir($dir, 0755, true)) {
				unset($product->thumbnail);
				pines_error("Error making image directory for product {$product->name}: $dir.");
				break;
			}
		}

		if (!$image->writeImage("{$dir}thumb.png")) {
			unset($product->thumbnail);
			pines_error("Error saving image: {$product->thumbnail}");
			continue;
		}
		$product->thumbnail = "{$dir}thumb.png";
	}
	break;
}

// Purchasing
$product->stock_type = $_REQUEST['stock_type'];
$product->custom_item = ($_REQUEST['custom_item'] == 'ON');
$product->vendors = (array) json_decode($_REQUEST['vendors']);
foreach ($product->vendors as &$cur_vendor) {
	$cur_vendor = array(
		'entity' => com_sales_vendor::factory((int) $cur_vendor->key),
		'sku' => $cur_vendor->values[1],
		'cost' => $cur_vendor->values[2],
		'link' => $cur_vendor->values[3]
	);
	if (!isset($cur_vendor['entity']->guid))
		$cur_vendor['entity'] = null;
}
unset($cur_vendor);

// Pricing
$product->pricing_method = $_REQUEST['pricing_method'];
$product->product_exp = strtotime($_REQUEST['product_exp']);
$product->unit_price = (float) $_REQUEST['unit_price'];
$product->margin = (float) $_REQUEST['margin'];
$product->floor = (float) $_REQUEST['floor'];
$product->ceiling = (float) $_REQUEST['ceiling'];
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
$product->return_checklists = array();
if (is_array($_REQUEST['return_checklists'])) {
	foreach ($_REQUEST['return_checklists'] as $cur_return_checklist_guid) {
		$cur_return_checklist = com_sales_return_checklist::factory((int) $cur_return_checklist_guid);
		if (isset($cur_return_checklist->guid))
			$product->return_checklists[] = $cur_return_checklist;
	}
}

// Attributes
$product->weight = (float) $_REQUEST['weight'];
$product->rma_after = (float) $_REQUEST['rma_after'];
$product->serialized = ($_REQUEST['serialized'] == 'ON');
$product->discountable = ($_REQUEST['discountable'] == 'ON');
$product->require_customer = ($_REQUEST['require_customer'] == 'ON');
$product->one_per_ticket = ($_REQUEST['one_per_ticket'] == 'ON');
$product->hide_on_invoice = ($_REQUEST['hide_on_invoice'] == 'ON');
$product->non_refundable = ($_REQUEST['non_refundable'] == 'ON');
$product->additional_barcodes = explode(',', $_REQUEST['additional_barcodes']);
$product->actions = (array) $_REQUEST['actions'];

// Commission
if ($_->config->com_sales->com_hrm) {
	$product->commissions = (array) json_decode($_REQUEST['commissions']);
	foreach ($product->commissions as $key => &$cur_commission) {
		$cur_commission = array(
			'group' => group::factory((int) $cur_commission->values[0]),
			'type' => $cur_commission->values[1],
			'amount' => (float) $cur_commission->values[2]
		);
		if (!isset($cur_commission['group']->guid) || !in_array($cur_commission['type'], array('spiff', 'percent_price', 'percent_line_total')))
			unset($product->commissions[$key]);
	}
	unset($cur_commission);
}

if ($_->config->com_sales->com_storefront) {
	// Storefront
	$product->alias = preg_replace('/[^\w\d-.]/', '', $_REQUEST['alias']);
	$product->show_in_storefront = ($_REQUEST['show_in_storefront'] == 'ON');
	$product->featured = ($_REQUEST['featured'] == 'ON');
	$product->featured_image = $_REQUEST['featured_image'];
	if (!$_->uploader->check($product->featured_image))
		$product->featured_image = null;
	// Build a list of categories.
	$categories = array();
	if (is_array($_REQUEST['categories']))
		$categories = array_map('intval', $_REQUEST['categories']);
	$categories = (array) $_->entity_manager->get_entities(
			array('class' => com_sales_category),
			array('&',
				'tag' => array('com_sales', 'category'),
				'data' => array('enabled', true)
			),
			array('|',
				'guid' => $categories
			)
		);
	// Build a list of specs.
	$specs = array();
	foreach ($categories as &$cur_category) {
		$specs = array_merge($specs, $cur_category->get_specs_all());
	}
	unset($categories, $cur_category);
	// Save specs.
	$product->specs = array();
	foreach ($specs as $key => $cur_spec) {
		switch ($cur_spec['type']) {
			case 'bool':
				$product->specs[$key] = ($_REQUEST[$key] == 'ON');
				break;
			case 'string':
				$product->specs[$key] = (string) $_REQUEST[$key];
				if ($cur_spec['restricted'] && !in_array($product->specs[$key], $cur_spec['options']))
					unset($product->specs[$key]);
				break;
			case 'float':
				$product->specs[$key] = (float) $_REQUEST[$key];
				if ($cur_spec['restricted'] && !in_array($product->specs[$key], $cur_spec['options']))
					unset($product->specs[$key]);
				break;
			default:
				break;
		}
	}
	unset($specs);

	// Page Head
	$product->title_use_name = ($_REQUEST['title_use_name'] == 'ON');
	$product->title = $_REQUEST['title'];
	$product->title_position = $_REQUEST['title_position'];
	if (!in_array($product->title_position, array('prepend', 'append', 'replace')))
		$product->title_position = 'prepend';
	$meta_tags = (array) json_decode($_REQUEST['meta_tags']);
	$product->meta_tags = array();
	foreach ($meta_tags as $cur_meta_tag) {
		if (!isset($cur_meta_tag->values[0], $cur_meta_tag->values[1]))
			continue;
		$product->meta_tags[] = array('name' => $cur_meta_tag->values[0], 'content' => $cur_meta_tag->values[1]);
	}
}

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
$test = $_->entity_manager->get_entity(array('class' => com_sales_product, 'skip_ac' => true), array('&', 'tag' => array('com_sales', 'product'), 'strict' => array('name', $product->name), '!guid' => $product->guid));
if (isset($test)) {
	$product->print_form();
	pines_notice('There is already a product with that name. Please choose a different name.');
	return;
}
$test = $_->entity_manager->get_entity(array('class' => com_sales_product, 'skip_ac' => true), array('&', 'tag' => array('com_sales', 'product'), 'strict' => array('sku', $product->sku), '!guid' => $product->guid));
if (isset($test)) {
	$product->print_form();
	pines_notice('There is already a product with that SKU. Please choose a different SKU.');
	return;
}

if ($product->show_in_storefront && $product->custom_item) {
	$product->print_form();
	pines_notice('Custom items cannot be displayed in the storefront. Select only one of these options.');
	return;
}

if ($_->config->com_sales->require_expiration && empty($product->product_exp)) {
	$product->print_form();
	pines_notice('You must provide a Product Expiration Date.');
	return;
}

if ($_->config->com_sales->global_products)
	$product->ac->other = 1;

if ($product->save()) {
	pines_notice('Saved product ['.$product->name.']');
	// Assign the product to the selected categories.
	// We have to do this here, because new products won't have a GUID until now.
	$categories = array();
	if (is_array($_REQUEST['categories']))
		$categories = array_map('intval', $_REQUEST['categories']);
	$all_categories = $_->entity_manager->get_entities(array('class' => com_sales_category), array('&', 'tag' => array('com_sales', 'category'), 'data' => array('enabled', true)));
	foreach($all_categories as &$cur_cat) {
		if (in_array($cur_cat->guid, $categories) && !$product->in_array($cur_cat->products)) {
			$cur_cat->products[] = $product;
			if (!$cur_cat->save())
				pines_error("Couldn't add product to category {$cur_cat->name}. Do you have permission?");
		} elseif (!in_array($cur_cat->guid, $categories) && $product->in_array($cur_cat->products)) {
			$key = $product->array_search($cur_cat->products);
			unset($cur_cat->products[$key]);
			if (!$cur_cat->save())
				pines_error("Couldn't remove product from category {$cur_cat->name}. Do you have permission?");
		}
	}
	unset($cur_cat);
	// Go through and delete any old product images that are no longer used.
	foreach ($prev_images as $cur_file) {
		if ($cur_file == $product->thumbnail)
			continue;
		foreach ($product->images as $cur_image) {
			if ($cur_file == $cur_image['file'] || $cur_file == $cur_image['thumbnail'])
				continue 2;
		}
		// This image is not used anymore, so delete it.
		if (!unlink($cur_file)) {
			pines_error("Can't delete old product image: $cur_file");
			continue;
		}
		// Now check if the directory is empty. If it is, remove it too.
		$cur_dir = dirname($cur_file);
		$files = array_diff(@scandir($cur_dir), array('.', '..'));
		if (!$files && !rmdir($cur_dir))
			pines_error("Can't delete empty product image directory: $cur_dir");
	}
} else {
	pines_error('Error saving product. Do you have permission?');
}

pines_redirect(pines_url('com_sales', 'product/list'));