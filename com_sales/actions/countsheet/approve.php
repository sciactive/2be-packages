<?php
/**
 * Provide a form to approve a countsheet.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if (!gatekeeper('com_sales/approvecountsheet') )
	punt_user(null, pines_url('com_sales', 'countsheet/edit', array('id' => $_REQUEST['id'])));

if (!isset($_REQUEST['id'])) {
	pines_error('Requested countsheet id is not accessible.');
	$_->com_sales->list_countsheets();
	return;
}
$entity = com_sales_countsheet::factory((int) $_REQUEST['id']);
/*
if (!$entity->final) {
	pines_notice('This countsheet has not been committed.');
	$_->com_sales->list_countsheets();
	return;
}
*/
$entity->print_review();