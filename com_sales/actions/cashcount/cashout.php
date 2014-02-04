<?php
/**
 * Provide a form to cash out a cash count.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/editcashcount') )
	punt_user(null, pines_url('com_sales', 'cashcount/cashout', array('id' => $_REQUEST['id'])));

// Default to the current cash count that is still open for this location.
if (!isset($_REQUEST['id']) || empty($_REQUEST['id'])) {
	$existing_counts = $_->entity_manager->get_entities(
			array('class' => com_sales_cashcount),
			array('&',
				'tag' => array('com_sales', 'cashcount'),
				'ref' => array('group', $_SESSION['user']->group)
			)
		);
	foreach ($existing_counts as $cur_count) {
		if (!in_array($cur_count->status, array('closed', 'flagged')))
			$cashcount = $cur_count;
	}
} else {
	$cashcount = com_sales_cashcount::factory((int) $_REQUEST['id']);
}

if (!isset($cashcount->guid)) {
	pines_error('Requested cash count id is not accessible.');
	$_->com_sales->list_cashcounts();
	return;
}
if ( isset($cashcount->group->guid) && !$cashcount->group->is($_SESSION['user']->group) ) {
	pines_notice('This cash count belongs to a different location.');
	$_->com_sales->list_cashcounts();
	return;
}
if (!$cashcount->final) {
	pines_notice('This cash count has not been committed.');
	$_->com_sales->list_cashcounts();
	return;
}
if ($cashcount->status == 'closed' || $cashcount->status == 'flagged') {
	pines_notice('This cash count has already been closed out.');
	$_->com_sales->list_cashcounts();
	return;
}

$cashcount->cash_out();