<?php
/**
 * List pages.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_content/listpages') )
	punt_user(null, pines_url('com_content', 'page/list'));

if (isset($_REQUEST['category'])) {
	$category = com_content_category::factory((int) $_REQUEST['category']);
	if (!isset($category->guid))
		$category = null;
}

$_->com_content->list_pages($category);