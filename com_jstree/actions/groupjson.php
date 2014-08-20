<?php
/**
 * Perform actions on groups, returning JSON.
 *
 * @package Components\jstree
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ($_REQUEST['all'] == 'true') {
	$locations = $_->user_manager->get_groups();
} elseif ($_REQUEST['primaries'] == 'true') {
	$my_group = group::factory((int) $_->config->com_user->highest_primary);
	if (!isset($my_group->guid))
		$locations = $_->user_manager->get_groups();
	else {
		$locations = $my_group->get_children();
		$descendants = array();
		foreach ($locations as &$cur_location) {
			$cur_location->parent = null;
			$cur_descendants = $cur_location->get_descendants();
			foreach ($cur_descendants as $cur_descendant) {
				if (!$cur_descendant->in_array($descendants) && !$cur_descendant->in_array($locations))
					$descendants[] = $cur_descendant;
			}
		}
		unset($cur_location);
		$locations = $locations + $descendants;
	}
} elseif (isset($_SESSION['user']->group)) {
	$my_group = clone $_SESSION['user']->group;
	$locations = $my_group->get_descendants();
	$my_group->parent = null;
	$locations[] = $my_group;
} else {
	$locations = $_->user_manager->get_groups();
}

$_->user_manager->group_sort($locations, 'name');

$groups_json_struct = $_->com_jstree->entity_json_struct($locations);
$_->page->ajax(json_encode($groups_json_struct));