<?php
/**
 * List a product's history (invoices, POs, transfers, countsheets, etc).
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/trackproducts') )
	punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'trackproduct', $_REQUEST));

if (!empty($_REQUEST['start_date'])) {
	$start_date = $_REQUEST['start_date'];
	if (strpos($start_date, '/') === false)
		$start_date = format_date($start_date, 'custom', 'm/d/Y');
	$start_date = strtotime($start_date.' 00:00');
} else {
	$start_date = strtotime('-1 week');
}
if (!empty($_REQUEST['end_date'])) {
	$end_date = $_REQUEST['end_date'];
	if (strpos($end_date, '/') === false)
		$end_date = format_date($end_date, 'custom', 'm/d/Y');
	$end_date = strtotime($end_date.' 23:59');
}
if ($_REQUEST['all_time'] == 'true') {
	$start_date = null;
	$end_date = null;
}
if (!empty($_REQUEST['location']) && $_REQUEST['location'] != 'all')
	$location = group::factory((int) $_REQUEST['location']);
$pines->com_sales->track_product($_REQUEST['serial'], $_REQUEST['sku'], $start_date, $end_date, $location);

?>