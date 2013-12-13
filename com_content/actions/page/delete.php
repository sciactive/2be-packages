<?php
/**
 * Delete a page.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_content/deletepage') )
	punt_user(null, pines_url('com_content', 'page/list'));

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_page) {
	$cur_entity = com_content_page::factory((int) $cur_page);
	if ( !isset($cur_entity->guid) || !$cur_entity->delete() )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_page;
}
if (empty($failed_deletes)) {
	pines_notice('Selected page(s) deleted successfully.');
} else {
	pines_error('Could not delete pages with given IDs: '.$failed_deletes);
}

pines_redirect(pines_url('com_content', 'page/list'));