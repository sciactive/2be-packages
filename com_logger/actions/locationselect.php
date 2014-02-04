<?php
/**
 * Select a location.
 *
 * @package Components\logger
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper() )
	punt_user(null, pines_url('com_logger', 'locationselect'));

$_->com_logger->location_select_form($_REQUEST['location'], ($_REQUEST['descendants'] == 'true'));