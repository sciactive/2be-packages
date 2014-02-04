<?php
/**
 * Provide a form to time customers on a floor.
 *
 * @package Components\customertimer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_customertimer/timefloor') )
	punt_user(null, pines_url('com_customertimer', 'timefloor', array('id' => $_REQUEST['id'])));

$entity = com_customertimer_floor::factory((int) $_REQUEST['id']);
if (!isset($entity->guid)) {
	pines_error('Requested floor id is not accessible.');
	return;
}
$entity->print_timer();