<?php
/**
 * Select a start and end date.
 *
 * @package Components\loan
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper() )
	punt_user(null, pines_url('com_loan', 'forms/dateselect'));

$_->com_loan->date_select_form($_REQUEST['all_time'] == 'true', empty($_REQUEST['start_date']) ? null : $_REQUEST['start_date'], empty($_REQUEST['end_date']) ? null : $_REQUEST['end_date']);
