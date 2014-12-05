<?php
/**
 * Provide a form to edit a countsheet.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!empty($_REQUEST['id'])) {
	if ( !gatekeeper('com_sales/editcountsheet') )
		punt_user(null, pines_url('com_sales', 'countsheet/edit', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_sales/newcountsheet') )
		punt_user(null, pines_url('com_sales', 'countsheet/edit'));
}

$entity = com_sales_countsheet::factory((int) $_REQUEST['id']);
// Ensure that only one countsheet is done at a time per assignment/location.
if ($_SESSION['user']->group->com_sales_task_countsheet) {
	if ( isset($entity->group->guid) && !$entity->group->is($_SESSION['user']->group) ) {
		pines_notice('This countsheet belongs to a different location.');
		$_->com_sales->list_countsheets();
		return;
	}
	if (!isset($entity->guid) && isset($_SESSION['user']->group)) {
		// Look for any countsheets that are waiting to be committed.
		$existing_sheets = $_->nymph->getEntities(
				array('class' => com_sales_countsheet),
				array('&',
					'tag' => array('com_sales', 'countsheet'),
					'ref' => array('group', $_SESSION['user']->group)
				)
			);
		foreach ($existing_sheets as $cur_sheet) {
			if (!$entity->is($cur_sheet) && !$cur_sheet->final) {
				pines_notice('This countsheet is already waiting to be committed for your location.');
				$cur_sheet->print_form();
				return;
			}
		}
	}
}
$entity->print_form();