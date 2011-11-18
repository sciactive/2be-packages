<?php
/**
 * Display the log.
 *
 * @package Pines
 * @subpackage com_logger
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_logger/view') )
	punt_user(null, pines_url('com_logger', 'view'));

$view = new module('com_logger', 'view', 'content');
if (file_exists($pines->config->com_logger->path)) {
	if (($view->log = file_get_contents($pines->config->com_logger->path)) === false)
		pines_error('Error reading log file '.$pines->config->com_logger->path);
} else {
	pines_error('Log file '.$pines->config->com_logger->path.' does not exist!');
}
if (empty($view->log)) $view->log = 'Log file is empty.';

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
	$view->all_time = true;
}
if (!empty($_REQUEST['location'])) {
	$location = group::factory((int) $_REQUEST['location']);
	if (!isset($location->guid))
		$location = null;
} else {
	$location = null;
}
$descendents = ($_REQUEST['descendents'] == 'true');

$view->start_date = $start_date;
$view->end_date = $end_date;
$view->location = $location;
$view->descendents = $descendents;

?>