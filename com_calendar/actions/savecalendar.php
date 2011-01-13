<?php
/**
 * Save all of the events for the company calendar.
 *
 * @package Pines
 * @subpackage com_calendar
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_calendar/editcalendar') )
	punt_user(null, pines_url('com_calendar', 'editcalendar'));

$pines->page->override = true;

if (isset($_REQUEST['events'])) {
	$events = explode(',', $_REQUEST['events']);

	date_default_timezone_set($_SESSION['user']->get_timezone());
	foreach ($events as $cur_event) {
		if (!empty($cur_event)) {
			$event_details = explode('|', $cur_event);
			$event = com_calendar_event::factory((int) $event_details[0]);

			if (isset($event->employee->guid) && !$event->time_off && !isset($event->appointment))
				$event->color = $event->employee->color;

			$event->event_id = $event_details[1];
			$event->start = strtotime($event_details[2]);
			$event->end = strtotime($event_details[3]);
			$event->all_day = ($event_details[4] == 'true') ? true : false;
			if (!isset($event->appointment)) {
				if ($event->all_day) {
					$days = ceil(($event->end - $event->start) / 86400);
					$event->scheduled = isset($event->employee->workday_length) ? $event->employee->workday_length : $pines->config->com_calendar->workday_length;
					$event->scheduled *= 3600 * $days;
				} else {
					$event->scheduled = $event->end - $event->start;
				}
			} else {
				if (isset($event->appointment->guid)) {
					$event->appointment->action_date = $event->start;
					$event->appointment->save();
				}
			}
			$event->ac->other = 1;
			$event->save();
		}
	}
}

?>