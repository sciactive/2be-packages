<?php
/**
 * List ESPs.
 *
 * @package Components\esp
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_esp/listplans') )
	punt_user(null, pines_url('com_esp', 'list'));

if ( !gatekeeper('com_esp/filterplans') || !isset($_REQUEST['show'])) {
	$_->com_esp->list_plans();
	return;
} else {
	$_->com_esp->list_plans($_REQUEST['show']);
	return;
}