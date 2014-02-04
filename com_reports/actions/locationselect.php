<?php
/**
 * Select a location.
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper() )
	punt_user(null, pines_url('com_reports', 'locationselect'));

$_->com_reports->location_select_form($_REQUEST['location'], ($_REQUEST['descendants'] == 'true'));