<?php
/**
 * Delete a product.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/deleteproduct') )
	punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'listproducts'));

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_product) {
	$cur_entity = com_sales_product::factory((int) $cur_product);
	if ( is_null($cur_entity->guid) || !$cur_entity->delete() )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_product;
}
if (empty($failed_deletes)) {
	pines_notice('Selected product(s) deleted successfully.');
} else {
	pines_error('Could not delete products with given IDs: '.$failed_deletes);
}

$pines->com_sales->list_products();
?>