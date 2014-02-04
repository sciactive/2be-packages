<?php
/**
 * Edit a quick work schedule for a company location.
 *
 * @package Components\calendar
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper() )
	punt_user(null, pines_url('com_calendar', 'editlineup'));

$location = group::factory((int)$_REQUEST['location']);
if (!isset($location->guid))
	return;

$_->com_calendar->lineup_form($location);