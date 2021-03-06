<?php
/**
 * Assign a countsheet to an employee.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!gatekeeper('com_sales/assigncountsheet') )
	punt_user(null, pines_url('com_sales', 'countsheet/assign', array('location' => $_REQUEST['location'])));

$location = group::factory((int) $_REQUEST['location']);
if (!isset($location->guid))
	$location = null;

if (!isset($location)) {
	pines_error('Requested location id is not accessible.');
	$_->com_sales->list_countsheets();
	return;
}
$location->com_sales_task_countsheet = true;
if ($location->save()) {
	pines_notice('Countsheet Assigned to ['.$location->name.']');
} else {
	pines_error('Error saving countsheet assignment. Do you have permission?');
}

pines_redirect(pines_url('com_sales', 'countsheet/list'));