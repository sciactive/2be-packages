<?php
/**
 * List sales for a sales report.
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_reports/reportsales') )
	punt_user(null, pines_url('com_reports', 'reportsales'));

if ( isset($_REQUEST['start']) ) {
	$start = strtotime($_REQUEST['start']);
	$end = strtotime($_REQUEST['end']);
} else {
	$start = strtotime('next monday', time() - 604800);
	$end = time();
}
if ( isset($_REQUEST['location']) ) {
	$location = group::factory((int) $_REQUEST['location']);
	$employee = com_hrm_employee::factory((int) $_REQUEST['employee']);
}
$descendants = ($_REQUEST['descendants'] == 'ON');

$_->com_reports->report_sales($start, $end, $location, $employee, $descendants);