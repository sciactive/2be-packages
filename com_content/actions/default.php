<?php
/**
 * Front page.
 *
 * @package Pines
 * @subpackage com_content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

$pages = $pines->entity_manager->get_entities(
		array('class' => com_content_page),
		array('|',
			'data' => array('publish_end', null),
			'gt' => array('publish_end', time())
		),
		array('&',
			'data' => array(
				array('enabled', true),
				array('show_front_page', true)
			),
			'lte' => array('publish_begin', time()),
			'tag' => array('com_content', 'page')
		)
	);

foreach ($pages as $cur_page) {
	$cur_page->print_page();
}

?>