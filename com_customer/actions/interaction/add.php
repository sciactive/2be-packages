<?php
/**
 * Log a customer interaction.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_customer/newinteraction') )
	punt_user(null, pines_url('com_customer', 'interaction/add'));

$customer = com_customer_customer::factory(intval($_REQUEST['customer']));
if (!isset($customer->guid))
	$customer = com_customer_company::factory(intval($_REQUEST['customer']));

if (!isset($customer->guid)) {
	$_->page->ajax('false');
	return;
}

$employee = com_hrm_employee::factory((int) $_REQUEST['employee']);
if (!isset($employee)) {
	$_->page->ajax('false');
	return;
}

$interaction = com_customer_interaction::factory();
$interaction->customer = $customer;
$interaction->employee = $employee;
if (!empty($_REQUEST['date']) || !empty($_REQUEST['time'])) {
	// Change the timezone to enter the event with the supplied timezone or user's timezone.
	$cur_timezone = date_default_timezone_get();
	if (!empty($_REQUEST['timezone']))
		date_default_timezone_set($_REQUEST['timezone']);
	else
		date_default_timezone_set($_SESSION['user']->get_timezone());
	$interaction->action_date = strtotime($_REQUEST['date'].$_REQUEST['time']);
} else {
	$interaction->action_date = time();
}
$interaction->type = $_REQUEST['type'];
$interaction->status = $_REQUEST['status'];
$interaction->comments = $_REQUEST['comments'];

$existing_appt = $_->nymph->getEntity(
		array('class' => com_customer_interaction),
		array('&',
			'data' => array('status', 'open'),
			'ref' => array('customer', $interaction->customer),
			'gte' => array('action_date', $interaction->action_date),
			'lte' => array('action_date', strtotime('+1 hour', $interaction->action_date))
		)
	);
if (isset($existing_appt->guid) && $interaction->guid != $existing_appt->guid) {
	$_->page->ajax('"conflict"');
	date_default_timezone_set($cur_timezone);
	return;
}

if ($_->config->com_customer->com_calendar) {
	// Create the interaction calendar event.
	$event = com_calendar_event::factory();
	$event->employee = $employee;
	$location = $employee->group;
	$event->appointment = true;
	$event->label = $interaction->type;
	foreach ($_->config->com_customer->interaction_types as $cur_type) {
		if (strpos($cur_type, $interaction->type))
			$symbol = explode(':', $cur_type);
	}
	$event->title = $symbol[0] .' '. $customer->name;
	$event->private = true;
	$event->all_day = false;
	$event->start = $interaction->action_date;
	$event->end = strtotime('+1 hour', $interaction->action_date);
	switch ($interaction->status) {
		case 'open':
		default:
			$event->color = 'greenyellow';
			break;
		case 'canceled':
			$event->color = 'gainsboro';
			break;
		case 'closed':
			$event->color = 'blue';
			break;
	}
	$event->information = '('.$interaction->employee->name.') '.$interaction->comments;
	$event->ac->other = 2;
	if (!$event->save()) {
		$_->page->ajax('false');
		date_default_timezone_set($cur_timezone);
		return;
	}

	$interaction->event = $event;
}

$interaction->ac->other = 2;

if ($interaction->save()) {
	if ($_->config->com_customer->com_calendar) {
		$event->appointment = $interaction;
		$event->group = $location;
		$event->save();
	}
	$_->page->ajax('true');
} else {
	$_->page->ajax('false');
}

date_default_timezone_set($cur_timezone);