<?php
/**
 * List daily cash drawer counts.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/listcashcounts') )
	punt_user(null, pines_url('com_sales', 'cashcount/list'));

if (!empty($_REQUEST['start_date']))
	$start_date = strtotime($_REQUEST['start_date'].' 00:00:00');
if (!empty($_REQUEST['end_date']))
	$end_date = strtotime($_REQUEST['end_date'].' 23:59:59') + 1;
if (!empty($_REQUEST['location'])) {
	$location = group::factory((int) $_REQUEST['location']);
	if (!isset($location->guid))
		$location = null;
}
$descendants = ($_REQUEST['descendants'] == 'true');
$finished = ($_REQUEST['finished'] == 'true');

$_->com_sales->list_cashcounts($start_date, $end_date, $location, $descendants, $finished);