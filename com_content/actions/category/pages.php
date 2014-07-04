<?php
/**
 * Get pages in a category, returning JSON.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

// TODO: Gatekeeper checks?

$_->page->override = true;
header('Content-Type: application/json');

$category = com_content_category::factory((int) $_REQUEST['id']);

if (!isset($category->guid)) {
	$_->page->override_doc(json_encode(array()));
	return;
}

$return = array();
foreach ($category->pages as $page) {
	if (!$page->enabled)
		continue;

	$json_struct = (object) array(
		'guid' => "$page->guid",
		'name' => $page->name
	);

	$return[] = $json_struct;
}

$_->page->override_doc(json_encode($return));