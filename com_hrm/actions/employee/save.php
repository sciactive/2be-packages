<?php
/**
 * Save changes to an employee.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_hrm/editemployee') )
	punt_user(null, pines_url('com_hrm', 'employee/list'));
$employee = com_hrm_employee::factory((int) $_REQUEST['id']);
if (!isset($employee->guid)) {
	pines_error('Requested employee id is not accessible.');
	return;
}

// General
$employee->nickname = $_REQUEST['nickname'];
if ($_->config->com_hrm->ssn_field && gatekeeper('com_hrm/showssn'))
	$employee->ssn = preg_replace('/\D/', '', $_REQUEST['ssn']);
$employee->new_hire = ($_REQUEST['new_hire'] == 'ON');
$employee->hire_date = empty($_REQUEST['hire_date']) ? null : strtotime($_REQUEST['hire_date']);
$employee->job_title = $_REQUEST['job_title'];
$employee->training_completion_date = empty($_REQUEST['training_completion_date']) ? null : strtotime($_REQUEST['training_completion_date']);
if (is_callable($_->editor, 'parse_input')) {
	$employee->description_pesource = $_REQUEST['description'];
	$employee->description = $_->editor->parse_input($_REQUEST['description']);
} else {
	$employee->description = $_REQUEST['description'];
}
if ($_->config->com_hrm->com_calendar)
	$employee->color = $_REQUEST['color'];
$employee->phone_ext = preg_replace('/\D/', '', $_REQUEST['phone_ext']);
$employee->other_phone = preg_replace('/\D/', '', $_REQUEST['other_phone']);
$employee->workday_length = $_REQUEST['workday_length'] != '' ?  (int) $_REQUEST['workday_length'] : null;
$employee->pay_type = $_REQUEST['pay_type'];
if ($employee->pay_type == 'commission') {
	$employee->pay_rate = 0.0;
} else {
	$employee->pay_rate = (float) $_REQUEST['pay_rate'];
}
// Attributes
$employee->employee_attributes = (array) json_decode($_REQUEST['attributes']);
foreach ($employee->employee_attributes as &$cur_attribute) {
	$array = array(
		'name' => $cur_attribute->values[0],
		'value' => $cur_attribute->values[1]
	);
	$cur_attribute = $array;
}
unset($cur_attribute);

if ($_->config->com_hrm->ssn_field_require && empty($employee->ssn)) {
	$employee->print_form();
	pines_notice('Please provide an SSN.');
	return;
}

if ($employee->save()) {
	pines_notice('Saved employee ['.$employee->name.']');
} else {
	pines_error('Error saving employee. Do you have permission?');
}

pines_redirect(pines_url('com_hrm', 'employee/list'));
