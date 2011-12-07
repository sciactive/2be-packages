<?php
/**
 * com_sales' information.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'POS',
	'author' => 'SciActive',
	'version' => '1.0.2',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'Point of Sales system',
	'description' => 'Manage products, inventory, sales, shipments, etc. Sell merchandise. Integrates with a cash drawer.',
	'depend' => array(
		'pines' => '<2',
		'service' => 'user_manager&entity_manager&editor&uploader',
		'component' => 'com_barcode&com_jquery&com_pgrid&com_pnotify&com_ptags&com_jstree&com_pform'
	),
	'recommend' => array(
		'component' => 'com_customer&com_hrm'
	),
	'abilities' => array(
		array('manager', 'Manager', 'User is a manager. This lets the user approve payments.'),
		array('discountstock', 'Discount Stock', 'User can give discounts on (discountable) products.'),
		array('receive', 'Receive Inventory', 'User can receive inventory into their stock.'),
		array('receivelocation', 'Receive Location', 'User can receive inventory into a location below them.'),
		array('seestock', 'See Stock', 'User can see stock.'),
		array('shipstock', 'Ship Stock', 'User can ship stock on an existing transfer.'),
		array('managestock', 'Manage Stock', 'User can transfer, ship, and adjust stock.'),
		array('warehouse', 'Warehouse Sales', 'User can fulfill warehouse sales.'),
		array('viewwarehouse', 'View Warehouse Sales', 'User can view pending warehouse sales.'),
		array('changeproduct', 'Change Products', 'User can change products on unfulfilled warehouse sales.'),
		array('totalsales', 'Total Sales', 'User can see sales totals.'),
		array('totalothersales', 'Total Other Sales', 'User can see sales totals of other locations.'),
		array('listsales', 'List Sales', 'User can see sales.'),
		array('newsale', 'Create Sales', 'User can create new sales.'),
		array('editsale', 'Edit Sales', 'User can edit current sales.'),
		array('voidownsale', 'Void Own Sales', 'User can void their own sales, returning any stock to inventory.'),
		array('voidsale', 'Void Sales', 'User can void any sales, returning any stock to inventory.'),
		array('swapsale', 'Swap Items', 'User can swap items for sales.'),
		array('swapsalesrep', 'Swap Salespeople', 'User can swap salespeople on completed sales/returns.'),
		array('deletesale', 'Delete Sales', 'User can delete current sales.'),
		array('listreturns', 'List Returns', 'User can see returns.'),
		array('newreturn', 'Create Returns', 'User can create new returns, without an attached sale.'),
		array('newreturnwsale', 'Create Sale Returns', 'User can create new returns, with an attached sale.'),
		array('newreturnpartial', 'Create Partial Returns', 'User can return part of a sale.'),
		array('editreturn', 'Edit Returns', 'User can edit current returns.'),
		//array('voidownreturn', 'Void Own Returns', 'User can void their own returns, removing any stock from inventory.'),
		//array('voidreturn', 'Void Returns', 'User can void any returns, removing any stock from inventory.'),
		array('deletereturn', 'Delete Returns', 'User can delete current returns.'),
		array('listmanufacturers', 'List Manufacturers', 'User can see manufacturers.'),
		array('newmanufacturer', 'Create Manufacturers', 'User can create new manufacturers.'),
		array('editmanufacturer', 'Edit Manufacturers', 'User can edit current manufacturers.'),
		array('deletemanufacturer', 'Delete Manufacturers', 'User can delete current manufacturers.'),
		array('listvendors', 'List Vendors', 'User can see vendors.'),
		array('newvendor', 'Create Vendors', 'User can create new vendors.'),
		array('editvendor', 'Edit Vendors', 'User can edit current vendors.'),
		array('deletevendor', 'Delete Vendors', 'User can delete current vendors.'),
		array('listshippers', 'List Shippers', 'User can see shippers.'),
		array('newshipper', 'Create Shippers', 'User can create new shippers.'),
		array('editshipper', 'Edit Shippers', 'User can edit current shippers.'),
		array('deleteshipper', 'Delete Shippers', 'User can delete current shippers.'),
		array('listtaxfees', 'List Taxes/Fees', 'User can see taxes/fees.'),
		array('newtaxfee', 'Create Taxes/Fees', 'User can create new taxes/fees.'),
		array('edittaxfee', 'Edit Taxes/Fees', 'User can edit current taxes/fees.'),
		array('deletetaxfee', 'Delete Taxes/Fees', 'User can delete current taxes/fees.'),
		array('listpaymenttypes', 'List Payment Types', 'User can see payment types.'),
		array('newpaymenttype', 'Create Payment Types', 'User can create new payment types.'),
		array('editpaymenttype', 'Edit Payment Types', 'User can edit current payment types.'),
		array('deletepaymenttype', 'Delete Payment Types', 'User can delete current payment types.'),
		array('listreturnchecklists', 'List Return Checklists', 'User can see return checklists.'),
		array('newreturnchecklist', 'Create Return Checklists', 'User can create new return checklists.'),
		array('editreturnchecklist', 'Edit Return Checklists', 'User can edit current return checklists.'),
		array('deletereturnchecklist', 'Delete Return Checklists', 'User can delete current return checklists.'),
		array('searchproducts', 'Search Products', 'User can search products. Needed for making sales, receiving inventory, etc.'),
		array('listproducts', 'List Products', 'User can see products.'),
		array('newproduct', 'Create Products', 'User can create new products.'),
		array('editproduct', 'Edit Products', 'User can edit current products.'),
		array('deleteproduct', 'Delete Products', 'User can delete current products.'),
		array('trackproducts', 'Track Products', 'User can track product history.'),
		array('listcategories', 'List Categories', 'User can see categories. (Not needed to see categories during a sale.)'),
		array('newcategory', 'Create Categories', 'User can create new categories.'),
		array('editcategory', 'Edit Categories', 'User can edit current categories.'),
		array('deletecategory', 'Delete Categories', 'User can delete current categories.'),
		array('listpos', 'List Purchase Orders', 'User can see POs.'),
		array('newpo', 'Create Purchase Orders', 'User can create new POs.'),
		array('editpo', 'Edit Purchase Orders', 'User can edit current POs.'),
		array('deletepo', 'Delete Purchase Orders', 'User can delete current POs.'),
		array('completepo', 'Complete Purchase Orders', 'User can mark partially received POs as complete.'),
		array('listcashcounts', 'List Cash Counts', 'User can see cash counts.'),
		array('newcashcount', 'Cash-In', 'User can create new cash counts.'),
		array('editcashcount', 'Cash-Out', 'User can close out cash counts.'),
		array('skimcashcount', 'Skim Cash Counts', 'User can skim from cash counts.'),
		array('depositcashcount', 'Deposit Cash Counts', 'User can deposit into cash counts.'),
		array('auditcashcount', 'Audit Cash Counts', 'User can audit cash counts.'),
		array('deletecashcount', 'Delete Cash Counts', 'User can delete current cash counts.'),
		array('approvecashcount', 'Approve Cash Counts', 'User can approve cash counts.'),
		array('assigncashcount', 'Assign Cash Counts', 'User can assign cash counts.'),
		array('listcountsheets', 'List Countsheets', 'User can see countsheets.'),
		array('newcountsheet', 'Create Countsheets', 'User can create new countsheets.'),
		array('editcountsheet', 'Edit Countsheets', 'User can edit current countsheets.'),
		array('deletecountsheet', 'Delete Countsheets', 'User can delete current countsheets.'),
		array('printcountsheet', 'Print Countsheets', 'User can print countsheets.'),
		array('uncommitcountsheet', 'Uncommit Countsheets', 'User can uncommit countsheets.'),
		array('approvecountsheet', 'Approve Countsheets', 'User can approve countsheets.'),
		array('assigncountsheet', 'Assign Countsheets', 'User can assign countsheets.'),
		array('overrideowner', 'Override Owner', 'User can override users/locations for sales/returns.')
	),
);

?>