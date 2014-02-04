<?php
/**
 * com_esp class.
 *
 * @package Components\esp
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_esp main class.
 *
 * @package Components\esp
 */
class com_esp extends component {
	/**
	 * Creates and attaches a module which lists all saved ESPs.
	 *
	 * @param string $show The method used to filter the ESPs.
	 * @return module The esp list module.
	 */
	function list_plans($show = null) {
		global $_;
		if (!isset($show)) {
			$show = json_decode($_SESSION['user']->pgrid_saved_states['com_esp/list'])->disposition;
			if (!isset($show))
				$show = 'all';
		}

		$module = new module('com_esp', 'list', 'content');

		if ($show == 'all') {
			$module->plans = $_->entity_manager->get_entities(array('class' => com_esp_plan), array('&', 'tag' => array('com_esp', 'esp')));
			$module->show = 'all';
		} else {
			$dispositions = array_map('preg_quote', explode(',', $show));
			$regex = '/'.implode('|', $dispositions).'/';
			
			// Only grab the returns that have a particular disposition.
			$module->plans = $_->entity_manager->get_entities(array('class' => com_esp_plan), array('&', 'tag' => array('com_esp', 'esp'), 'match' => array('status', $regex)));
			$module->show = $show;
		}

		if ( empty($module->plans) )
			pines_notice('There are no ESPs.');

		return $module;
	}
}