<?php
/**
 * Save a tab.
 *
 * @package Components\dash
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
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
if (!empty($_REQUEST['key']) && !isset($dashboard->tabs[$_REQUEST['key']])) {
	pines_notice('Requested tab is invalid.');
	return;
}

// For both new and edited tabs:
// Build a column array.
$columns = array();
foreach (json_decode($_REQUEST['columns'], true) as $cur_column) {
	if (isset($cur_column['key']))
		$key = (string) $cur_column['key'];
	else
		$key = uniqid();
	$columns[$key] = array(
		'size' => (int) $cur_column['size'],
		'widgets' => array()
	);
}

// Now we have our column array, and things get different if this is a new tab.
if (empty($_REQUEST['key'])) {
	// New tab.
	$tab_key = uniqid();
	$tab_name = trim($_REQUEST['name']);
	$dashboard->tabs[$tab_key] = array(
		'name' => empty($tab_name) ? 'Untitled Tab' : $tab_name,
		'buttons' => array(),
		'buttons_size' => 'large',
		'columns' => $columns
	);
} else {
	// Current tab.
	$tab_key = $_REQUEST['key'];
	$dashboard->tabs[$tab_key]['name'] = $_REQUEST['name'];
	// Save the old columns.
	$old_columns = $dashboard->tabs[$_REQUEST['key']]['columns'];
	// Now copy widgets to the new columns.
	foreach ($old_columns as $col_key => $cur_column) {
		if (isset($columns[$col_key]))
			$columns[$col_key]['widgets'] = $cur_column['widgets'];
		else {
			// Since this column was deleted, copy its widgets into a current column.
			$key = key($columns);
			foreach ($cur_column['widgets'] as $wkey => $widget)
				$columns[$key]['widgets'][$wkey] = $widget;
		}
	}
	// Now put in the new columns.
	$dashboard->tabs[$_REQUEST['key']]['columns'] = $columns;
}

if (!$dashboard->save())
	pines_error('An error occured while trying to save the tab.');

pines_redirect(pines_url('com_dash', null, array('id' => (string) $dashboard->guid, 'tab' => $tab_key)));