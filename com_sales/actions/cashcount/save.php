<?php
/**
 * Save changes to a cash count.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( isset($_REQUEST['id']) ) {
	if ( !gatekeeper('com_sales/editcashcount') )
		punt_user(null, pines_url('com_sales', 'cashcount/list'));
	$cashcount = com_sales_cashcount::factory((int) $_REQUEST['id']);
	if (!isset($cashcount->guid)) {
		pines_error('Requested cash count id is not accessible.');
		return;
	}
	if ($cashcount->final) {
		pines_notice('This cash count has already been cashed-in.');
		return;
	}
} else {
	if ( !gatekeeper('com_sales/newcashcount') )
		punt_user(null, pines_url('com_sales', 'cashcount/list'));
	$cashcount = com_sales_cashcount::factory();
}

$cashcount->comments = $_REQUEST['comments'];
$cashcount->float = 0;
// Save the total count of each different denomination.
foreach ($cashcount->currency as $key => $cur_currency) {
	// The float is the total amount of money in the drawer to begin with.
	$cashcount->count[$key] = (int) $_REQUEST["count_$key"];
	$cashcount->float += ((float) $cur_currency) * $cashcount->count[$key];
}

if ($_REQUEST['save'] == 'commit') {
	$cashcount->final = true;
	// Complete the cashcount assignment if one exists for this group.
	if (isset($_SESSION['user']->group->com_sales_task_cashcount)) {
		pines_session('write');
		unset($_SESSION['user']->group->com_sales_task_cashcount);
		$_SESSION['user']->group->save();
		pines_session('close');
	}
}

if ($_->config->com_sales->global_cashcounts)
	$cashcount->ac->other = 1;

if ($cashcount->save()) {
	if ($cashcount->final) {
		pines_notice('Cash Count ['.$cashcount->guid.'] Cashed-In with $'.$cashcount->float);
	} else {
		pines_notice('Saved Cash Count ['.$cashcount->guid.']');
	}
} else {
	$cashcount->print_form();
	pines_error('Error saving Cash Count. Do you have permission?');
	return;
}

pines_redirect(pines_url('com_sales', 'cashcount/list'));