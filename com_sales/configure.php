<?php
/**
 * com_sales' configuration.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

return array(
	array(
		'name' => 'dec',
		'cname' => 'Visible Decimal Places',
		'description' => 'Decimal numbers, though stored in the database more accurately, will only be displayed to this number of places.',
		'value' => 2,
	),
	array(
		'name' => 'global_products',
		'cname' => 'Globalize Products',
		'description' => 'Ensure that every user can access all products by setting the "other" access control to read.',
		'value' => true,
	),
	array(
		'name' => 'global_manufacturers',
		'cname' => 'Globalize Manufacturers',
		'description' => 'Ensure that every user can access all manufacturers by setting the "other" access control to read.',
		'value' => true,
	),
	array(
		'name' => 'global_vendors',
		'cname' => 'Globalize Vendors',
		'description' => 'Ensure that every user can access all vendors by setting the "other" access control to read.',
		'value' => true,
	),
	array(
		'name' => 'global_shippers',
		'cname' => 'Globalize Shippers',
		'description' => 'Ensure that every user can access all shippers by setting the "other" access control to read.',
		'value' => true,
	),
	array(
		'name' => 'global_payment_types',
		'cname' => 'Globalize Payment Types',
		'description' => 'Ensure that every user can access all payment types by setting the "other" access control to read.',
		'value' => true,
	),
	array(
		'name' => 'global_tax_fees',
		'cname' => 'Globalize Taxes/Fees',
		'description' => 'Ensure that every user can access all taxes and fees by setting the "other" access control to read.',
		'value' => true,
	),
	array(
		'name' => 'receipt_note_label',
		'cname' => 'Receipt Note Label',
		'description' => 'The receipt note will be appended to all receipts.',
		'value' => 'Return Policy:',
	),
	array(
		'name' => 'receipt_note_text',
		'cname' => 'Receipt Note Text',
		'description' => 'The receipt note will be appended to all receipts.',
		'value' => 'You (Buyer) have 14 (fourteen) calendar days from the date on your Sales Invoice to Return the item(s) purchased. All returns will be subject to a 15% restocking fee made payable at the time of return. All restocking fees must be in the form of credit card or money order. All returns must be in original condition including item purchased, packaging, accessories, software, cords or other items. We reserve the right to request identification and to deny any return.',
	),
	array(
		'name' => 'invoice_note_label',
		'cname' => 'Invoice Note Label',
		'description' => 'The invoice note will be appended to all invoices.',
		'value' => 'Return Policy:',
	),
	array(
		'name' => 'invoice_note_text',
		'cname' => 'Invoice Note Text',
		'description' => 'The invoice note will be appended to all invoices.',
		'value' => 'You (Buyer) have 14 (fourteen) calendar days from the date on your Sales Invoice to Return the item(s) purchased. All returns will be subject to a 15% restocking fee made payable at the time of return. All restocking fees must be in the form of credit card or money order. All returns must be in original condition including item purchased, packaging, accessories, software, cords or other items. We reserve the right to request identification and to deny any return.',
	),
	array(
		'name' => 'quote_note_label',
		'cname' => 'Quote Note Label',
		'description' => 'The quote note will be appended to all quotes.',
		'value' => 'Return Policy:',
	),
	array(
		'name' => 'quote_note_text',
		'cname' => 'Quote Note Text',
		'description' => 'The quote note will be appended to all quotes.',
		'value' => 'You (Buyer) have 14 (fourteen) calendar days from the date on your Sales Invoice to Return the item(s) purchased. All returns will be subject to a 15% restocking fee made payable at the time of return. All restocking fees must be in the form of credit card or money order. All returns must be in original condition including item purchased, packaging, accessories, software, cords or other items. We reserve the right to request identification and to deny any return.',
	),
	array(
		'name' => 'email_receipt',
		'cname' => 'Email Receipt',
		'description' => 'Email a copy of the receipt to the customer when the sale is tendered.',
		'value' => true,
	),
	array(
		'name' => 'email_from_address',
		'cname' => 'From Address',
		'description' => 'The address the email will be sent from.',
		'value' => 'sales@sciactive.com',
	),
	array(
		'name' => 'cash_drawer',
		'cname' => 'Cash Drawer',
		'description' => 'Integrate the POS with a cash drawer. (Requires the Pines Cash Drawer Firefox addon.)',
		'value' => true,
	),
	array(
		'name' => 'cash_drawer_group',
		'cname' => 'Cash Drawer Group',
		'description' => 'Only use the cash drawer for users in this group. (Enter the group\'s GUID or 0 for all groups.)',
		'value' => 0,
	),
);

?>