<?php
/**
 * Select a company location.
 *
 * @package Components\calendar
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper() )
	punt_user(null, pines_url('com_calendar', 'locationselect'));

$_->com_calendar->location_select_form($_REQUEST['location'], ($_REQUEST['descendants'] == 'true'));