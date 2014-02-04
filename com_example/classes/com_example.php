<?php
/**
 * com_example class.
 *
 * @package Components\example
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_example main class.
 *
 * @package Components\example
 */
class com_example extends component {
	/**
	 * Creates and attaches a module which lists foobars.
	 * @return module The module.
	 */
	public function list_foobars() {
		global $_;

		$module = new module('com_example', 'foobar/list', 'content');

		$module->foobars = $_->entity_manager->get_entities(
				array('class' => com_example_foobar),
				array('&',
					'tag' => array('com_example', 'foobar')
				)
			);

		if ( empty($module->foobars) )
			pines_notice('No foobars found.');

		return $module;
	}

	/**
	 * Creates and attaches example modules in various positions.
	 */
	public function print_content() {
		$module = new module('com_example', 'content/short', 'content_top_left');
		$module = new module('com_example', 'content/short', 'content_top_right');
		$module = new module('com_example', 'content/medium', 'pre_content');
		$module = new module('com_example', 'content/breadcrumbs', 'breadcrumbs');
		$module = new module('com_example', 'content/long', 'content');
		$module = new module('com_example', 'content/medium', 'post_content');
		$module = new module('com_example', 'content/short', 'content_bottom_left');
		$module = new module('com_example', 'content/short', 'content_bottom_right');
		$module = new module('com_example', 'content/short', 'left');
		$module = new module('com_example', 'content/short', 'right');
		//$module = new module('com_example', 'content/medium', 'left');
		$module = new module('com_example', 'content/medium', 'right');
		$module = new module('com_example', 'content/short', 'top');
		$module = new module('com_example', 'content/short', 'header');
		$module = new module('com_example', 'content/short', 'header_right');
		$module = new module('com_example', 'content/medium', 'footer');
		$module = new module('com_example', 'content/short', 'bottom');
	}
}