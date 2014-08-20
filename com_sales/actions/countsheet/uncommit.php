<?php
/**
 * Uncommit a countsheet.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/uncommitcountsheet') )
	punt_user(null, pines_url('com_sales', 'countsheet/list'));

$countsheet = com_sales_countsheet::factory((int) $_REQUEST['id']);
if (!isset($countsheet->guid)) {
	$_->page->ajax(json_encode(array(false, 'Requested countsheet id is not accessible.')));
	return;
}
if (!$countsheet->final) {
	$_->page->ajax(json_encode(array(false, 'Requested countsheet has not been committed.')));
	return;
}

$countsheet->final = false;

if ($countsheet->save())
	$_->page->ajax(json_encode(array(true, 'Countsheet has been uncommitted.')));
else
	$_->page->ajax(json_encode(array(false, 'Error saving countsheet. Do you have permission?')));