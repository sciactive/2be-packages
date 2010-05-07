<?php
/**
 * Perform actions on groups, returning JSON.
 *
 * @package Pines
 * @subpackage com_hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/viewcalendar') )
	punt_user('You don\'t have necessary permission.', pines_url('com_hrm', 'groupjson', $_REQUEST));

$pines->page->override = true;

if (isset($_SESSION['user']->group)) {
	$my_group = clone $_SESSION['user']->group;
	$locations = $pines->user_manager->get_group_descendents($my_group);
	$my_group->parent = null;
	$locations[] = $my_group;
} else {
	$locations = $pines->user_manager->get_groups();
}

$groups_json_struct = $pines->com_sales->category_json_struct($locations);
$pines->page->override_doc(json_encode($groups_json_struct));

?>