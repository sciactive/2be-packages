<?php
/**
 * Delete a category.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/deletecategory') )
	punt_user(null, pines_url('com_sales', 'category/list'));

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_category) {
	$cur_entity = com_sales_category::factory((int) $cur_category);
	if ( !isset($cur_entity->guid) || !$cur_entity->delete() )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_category;
}
if (empty($failed_deletes)) {
	pines_notice('Selected category(s) deleted successfully.');
} else {
	pines_error('Could not delete categories with given IDs: '.$failed_deletes."\n\nThey may have already been deleted.");
}

pines_redirect(pines_url('com_sales', 'category/list'));