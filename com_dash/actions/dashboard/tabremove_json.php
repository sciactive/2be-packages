<?php
/**
 * Remove a tab.
 *
 * @package Components\dash
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_dash/dash') || !gatekeeper('com_dash/editdash') )
	punt_user(null, pines_url('com_dash'));

if (!empty($_REQUEST['id']) && gatekeeper('com_dash/manage'))
	$dashboard = com_dash_dashboard::factory((int) $_REQUEST['id']);
else
	$dashboard =& $_SESSION['user']->dashboard;
if (!isset($dashboard->guid))
	throw new HttpClientException(null, 400);
if ($dashboard->locked && !gatekeeper('com_dash/manage'))
	throw new HttpClientException(null, 403);

// Check the requested tab.
if (!isset($dashboard->tabs[$_REQUEST['key']]))
	throw new HttpClientException(null, 400);
// Check that it's not the last tab.
if (count($dashboard->tabs) <= 1) {
	$_->page->ajax(json_encode('last'));
	return;
}

// Remove it.
unset($dashboard->tabs[$_REQUEST['key']]);

$_->page->ajax(json_encode($dashboard->save()));