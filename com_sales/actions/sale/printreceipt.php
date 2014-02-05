<?php
/**
 * Provide a receipt file of a sale.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/editsale') && !gatekeeper('com_sales/newsale') )
	punt_user(null, pines_url('com_sales', 'sale/printreceipt', array('id' => $_REQUEST['id'])));

$entity = com_sales_sale::factory((int) $_REQUEST['id']);

header('Content-Type: application/x-wonder-receipt');
// This makes it impossible to automatically open the file in Firefox because of
// RFC 2183 -- See bug 331259.
//header('Content-Disposition: attachment; filename="receipt"');
header('Content-Transfer-Encoding: binary');
$_->page->override = true;
$_->page->override_doc($entity->receipt_text(48, 72));