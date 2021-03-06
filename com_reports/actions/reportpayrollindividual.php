<?php
/**
 * Report and individual payroll.
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Kirk Johnson <kirk@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_reports/reportpayroll') )
	punt_user(null, pines_url('com_reports', 'reportpayrollindividual', $_REQUEST));

if (!empty($_REQUEST['start_date'])) {
	$start_date = $_REQUEST['start_date'];
	if (strpos($start_date, '-') === false)
		$start_date = format_date($start_date, 'date_sort');
	$start_date = strtotime($start_date.' 00:00:00');
} else {
	$start_date = strtotime('-1 week');
}
if (!empty($_REQUEST['end_date'])) {
	$end_date = $_REQUEST['end_date'];
	if (strpos($end_date, '-') === false)
		$end_date = format_date($end_date, 'date_sort');
	$end_date = strtotime($end_date.' 23:59:59') + 1;
} else {
	$end_date = strtotime('now');
}
if ($_REQUEST['all_time'] == 'true') {
	$start_date = null;
	$end_date = null;
}
if (!empty($_REQUEST['location']))
	$location = group::factory((int) $_REQUEST['location']);
$descendants = ($_REQUEST['descendants'] == 'true');

$emp = com_hrm_employee::factory((int) $_REQUEST['id']);
$hours = $_REQUEST['hours'];
$payperhour = $_REQUEST['payperhour'];
$salary = $_REQUEST['salary'];
$total = $_REQUEST['total'];
$commission = str_replace('$', '', $_REQUEST['commission']);
$hourreport = $_REQUEST['hourreport'];
$adjustment = (float) str_replace('$', '', $_REQUEST['adjustment']);
$reghours = (float) str_replace('$', '', $_REQUEST['reghours']);
$overtime = (float) str_replace('$', '', $_REQUEST['overtime']);

$_->com_reports->report_payroll_individual($start_date, $end_date, $emp, $payperhour, $hours, $total, $salary, $commission, $hourreport, $adjustment, $reghours, $overtime);