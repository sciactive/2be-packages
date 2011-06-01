<?php
/**
 * Delete a widget.
 *
 * @package Pines
 * @subpackage com_example
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_example/deletewidget') )
	punt_user(null, pines_url('com_example', 'widget/list'));

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_widget) {
	$cur_entity = com_example_widget::factory((int) $cur_widget);
	if ( !isset($cur_entity->guid) || !$cur_entity->delete() )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_widget;
}
if (empty($failed_deletes)) {
	pines_notice('Selected widget(s) deleted successfully.');
} else {
	pines_error('Could not delete widgets with given IDs: '.$failed_deletes);
}

pines_redirect(pines_url('com_example', 'widget/list'));

?>