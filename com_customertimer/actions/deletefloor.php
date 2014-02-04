<?php
/**
 * Delete a floor.
 *
 * @package Components\customertimer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_customertimer/deletefloor') )
	punt_user(null, pines_url('com_customertimer', 'listfloors'));

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_floor) {
	$cur_entity = com_customertimer_floor::factory((int) $cur_floor);
	if ( !isset($cur_entity->guid) || !$cur_entity->delete() )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_floor;
}
if (empty($failed_deletes)) {
	pines_notice('Selected floor(s) deleted successfully.');
} else {
	pines_error('Could not delete floors with given IDs: '.$failed_deletes);
}

pines_redirect(pines_url('com_customertimer', 'listfloors'));