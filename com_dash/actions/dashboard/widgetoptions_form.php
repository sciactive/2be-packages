<?php
/**
 * Get a widget's options form.
 *
 * @package Pines
 * @subpackage com_dash
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_dash/dash') || !gatekeeper('com_dash/editdash') )
	punt_user(null, pines_url('com_dash'));

$pines->page->override = true;

if (!empty($_REQUEST['id']) && gatekeeper('com_dash/manage'))
	$dashboard = com_dash_dashboard::factory((int) $_REQUEST['id']);
else
	$dashboard =& $_SESSION['user']->dashboard;
if (!isset($dashboard->guid))
	throw new HttpClientException(null, 400);
if ($dashboard->locked && !gatekeeper('com_dash/manage'))
	throw new HttpClientException(null, 403);

// Get the widget entry.
$widget_entry = $dashboard->widget($_REQUEST['key']);
if (!$widget_entry)
	throw new HttpClientException(null, 400);

// Get the view and make a module.
$def = $pines->com_dash->get_widget_def($widget_entry);
$view = $def['form'];
if (!isset($view)) {
	$pines->page->override_doc('false');
	return;
}
$module = new module($widget_entry['component'], $view);

$content = $module->render();
$pines->page->override_doc($content);

?>