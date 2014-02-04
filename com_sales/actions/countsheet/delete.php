<?php
/**
 * Delete a countsheet.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/deletecountsheet') )
	punt_user(null, pines_url('com_sales', 'countsheet/list'));

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_sheet) {
	$cur_entity = com_sales_countsheet::factory((int) $cur_sheet);
	if ( !isset($cur_entity->guid) || !$cur_entity->delete() )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_sheet;
}
if (empty($failed_deletes)) {
	pines_notice('Selected countsheet(s) deleted successfully.');
} else {
	pines_error('Could not delete countsheets with given IDs: '.$failed_deletes);
}

pines_redirect(pines_url('com_sales', 'countsheet/list'));