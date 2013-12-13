<?php
/**
 * Clock an employee in or out, returning their status in JSON.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_hrm/clock') && !gatekeeper('com_hrm/manageclock') )
	punt_user(null, pines_url('com_hrm', 'employee/timeclock/clock', $_REQUEST));

$pines->page->override = true;
header('Content-Type: application/json');

if ($_REQUEST['id'] == 'self') {
	$employee = com_hrm_employee::factory($_SESSION['user']->guid);
	if ($pines->config->com_hrm->timeclock_verify_pin && !empty($_SESSION['user']->pin) && $_REQUEST['pin'] != $_SESSION['user']->pin) {
		$pines->page->override_doc(json_encode('pin'));
		return;
	}
} else {
	if ( !gatekeeper('com_hrm/manageclock') )
		punt_user(null, pines_url('com_hrm', 'employee/timeclock/clock', $_REQUEST));
	$employee = com_hrm_employee::factory((int) $_REQUEST['id']);
}

if (!isset($employee->guid)) {
	$pines->page->override_doc('false');
	return;
}

if ($employee->timeclock->clocked_in_time()) {
	$success = $employee->timeclock->clock_out($_REQUEST['comment']);
} else {
	$success = $employee->timeclock->clock_in();
}

if (!$success || !$employee->save()) {
	$pines->page->override_doc('false');
	return;
}


$pines->page->override_doc(json_encode($employee->timeclock->clocked_in_time()));