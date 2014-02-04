<?php
/**
 * Provide a form to edit a category.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if (!empty($_REQUEST['id'])) {
	if ( !gatekeeper('com_content/editcategory') )
		punt_user(null, pines_url('com_content', 'category/edit', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_content/newcategory') )
		punt_user(null, pines_url('com_content', 'category/edit'));
}

$entity = com_content_category::factory((int) $_REQUEST['id']);
$entity->print_form();