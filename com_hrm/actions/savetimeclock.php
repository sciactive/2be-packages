<?php
/**
 * Save changes to an employees timeclock.
 *
 * @package Pines
 * @subpackage com_hrm
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_hrm/manageclock') )
	punt_user('You don\'t have necessary permission.', pines_url('com_hrm', 'edittimeclock', array('id' => $_REQUEST['id']), false));

$employee = com_hrm_employee::factory((int) $_REQUEST['id']);
if (is_null($employee->guid)) {
	display_error('Requested employee id is not accessible.');
	return;
}

$employee->timeclock = array();

$clock = json_decode($_REQUEST['clock']);
if (!is_array($clock))
	$clock = array();

foreach($clock as $cur_entry) {
	$employee->timeclock[] = array(
		'time' => (int) $cur_entry->time,
		'status' => ($cur_entry->status == 'out' ? 'out' : 'in')
	);
}

if ($employee->save()) {
	display_notice("Saved timeclock for {$employee->name}.");
} else {
	display_error('Error saving timeclock. Do you have permission?');
}

$pines->com_hrm->list_timeclocks();
?>