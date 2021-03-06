<?php
/**
 * Delete a transfer.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/managestock') )
	punt_user(null, pines_url('com_sales', 'transfer/list'));

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_transfer) {
	$cur_entity = com_sales_transfer::factory((int) $cur_transfer);
	if ( !isset($cur_entity->guid) || !$cur_entity->delete() )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_transfer;
}
if (empty($failed_deletes)) {
	pines_notice('Selected transfer(s) deleted successfully.');
} else {
	pines_error('Could not delete transfers with given IDs: '.$failed_deletes);
	pines_notice('Note that transfers cannot be deleted after items have been received on them.');
}

pines_redirect(pines_url('com_sales', 'transfer/list'));