<?php
/**
 * Receive inventory.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/receive') ) {
	$config->user_manager->punt_user("You don't have necessary permission.", pines_url('com_sales', 'receive', null, false));
	return;
}

if (is_null($_REQUEST['products'])) {
	$config->run_sales->print_receive_form('com_sales', 'receive');
	return;
}

$products_json = json_decode($_REQUEST['products']);
if (!is_array($products_json)) {
	display_notice('Invalid product list!');
	$config->run_sales->print_receive_form('com_sales', 'receive');
	return;
}
$products = array();
foreach ($products_json as $key => $cur_product) {
	$products[$key] = array(
		'product_code' => $cur_product->values[0],
		'serial' => $cur_product->values[1],
		'quantity' => intval($cur_product->values[2])
	);
}

foreach ($products as $cur_product) {
	$cur_product_entity = $config->run_sales->get_product_by_code($cur_product['product_code']);
	if (is_null($cur_product_entity)) {
		display_notice("Product with code {$cur_product['product_code']} not found! Skipping...");
		continue;
	}
	if ($cur_product_entity->serialized && empty($cur_product['serial'])) {
		display_notice("Product [{$cur_product_entity->name}] with code {$cur_product['product_code']} requires a serial! Skipping...");
		continue;
	}
	for ($i = 0; $i < $cur_product['quantity']; $i++) {
		$stock_entity = new entity('com_sales', 'stock_entry');
		$stock_entity->product_guid = $cur_product_entity->guid;
		$stock_entity->location = $_SESSION['user']->gid;
		var_dump($stock_entity);
	}
}

?>