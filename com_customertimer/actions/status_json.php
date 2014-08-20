<?php
/**
 * Return a JSON structure of customers and their statuses.
 *
 * @package Components\customertimer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_customertimer/timefloor') )
	punt_user(null, pines_url('com_customertimer', 'status'));

$floor = com_customertimer_floor::factory((int) $_REQUEST['floor']);
$return = array();

foreach ($floor->active_stations as $cur_station => $cur_entry) {
	if (!$cur_entry['customer']) {
		// TODO: Make a warning about this rogue entry.
		continue;
	}
	$session_info = $cur_entry['customer']->com_customertimer_get_session_info($floor, $cur_station);
	$return[] = (object) array(
		'guid' => "{$cur_entry['customer']->guid}",
		'name' => $cur_entry['customer']->name,
		'login_time' => $cur_entry['time_in'],
		'points' => $cur_entry['customer']->points,
		'ses_minutes' => $session_info['minutes'],
		'ses_points' => $session_info['points'],
		'other_minutes' => $session_info['other_minutes'],
		'other_points' => $session_info['other_points'],
		'points_remain' => $session_info['points_remain'],
		'station' => $cur_station
	);
}

$_->page->ajax(json_encode($return));