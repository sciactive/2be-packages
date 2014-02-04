<?php
/**
 * com_notes class.
 *
 * @package Components\notes
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_notes main class.
 *
 * @package Components\notes
 */
class com_notes extends component {
	/**
	 * Creates and attaches a module which lists threads.
	 * @return module The module.
	 */
	public function list_threads() {
		global $_;

		$module = new module('com_notes', 'thread/list', 'content');

		$module->threads = $_->entity_manager->get_entities(
				array('class' => com_notes_thread),
				array('&',
					'tag' => array('com_notes', 'thread')
				)
			);

		if ( empty($module->threads) )
			pines_notice('No threads found.');

		return $module;
	}
}