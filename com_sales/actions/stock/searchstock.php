<?php
/**
 * Search for Stock matching Serial Query.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/seestock') )
	punt_user(null, pines_url('com_sales', 'stock/list', $_REQUEST));

// The query.
$query = trim($_REQUEST['serial']);

// Determine the type of query.
if (preg_match('/^\s*$/', $query)) {
	// Nothing was queried.
	$stock_entries = array();
} elseif ($query == '*') {
	// The user wants to see all stock entries.
	$stock_entries = (array) $_->entity_manager->get_entities(
			array('class' => com_sales_stock, 'reverse' => true),
			array('&',
				'tag' => array('com_sales', 'stock'),
				'isset' => 'serial'
			)
		);
} else {
	// Select by query.
	$stock_entries = (array) $_->entity_manager->get_entities(
			array('class' => com_sales_stock, 'reverse' => true),
			array('&',
				'tag' => array('com_sales', 'stock'),
				'strict' => array('serial', $query)
			)
		);
}


// Build JSON array.
foreach ($stock_entries as &$stock_entry) {
	// Get last transaction
	if (!isset($stock_entry->guid))
		$last_transaction = "";
	else
		$last_transaction = preg_replace('/\bpo\b/i', 'PO', ucwords(str_replace('_', ' ', $stock_entry->last_reason())));
	$stock_entry = array(
		'guid' => "$stock_entry->guid",
		'product_guid' => "{$stock_entry->product->guid}",
		'enabled' => $stock_entry->product->enabled,
		'product_sku' => $stock_entry->product->sku,
		'product_name' => $stock_entry->product->name,
		'location_name' => isset($stock_entry->location->guid) ? $stock_entry->location->name : null,
		'serial' => $stock_entry->serial,
		'last_transaction' => $last_transaction
	);
	if ($_->config->com_sales->enable_manufacturers) {
		$stock_entry['manufacturer'] = $stock_entry->product->manufacturer->name;
		$stock_entry['manufacturer_sku'] = $stock_entry->product->manufacturer_sku;
	}
}
unset($stock_entry);
if (!$stock_entries)
	$stock_entries = null;

$result = array();
$result['stock_entries'] = $stock_entries;

$_->page->ajax(json_encode($result));