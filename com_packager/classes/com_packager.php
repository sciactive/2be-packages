<?php
/**
 * com_packager class.
 *
 * @package Components\packager
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_packager main class.
 *
 * @package Components\packager
 */
class com_packager extends component {
	/**
	 * Creates and attaches a module which lists packages.
	 * @return module The module.
	 */
	public function list_packages() {
		global $_;

		$module = new module('com_packager', 'package/list', 'content');

		$module->packages = $_->entity_manager->get_entities(array('class' => com_packager_package), array('&', 'tag' => array('com_packager', 'package')));

		if ( empty($module->packages) )
			pines_notice('There are no packages.');

		return $module;
	}

	/**
	 * Creates a package wizard.
	 */
	public function package_wizard() {
		global $_;
		$module = new module('com_packager', 'package/wizard', 'content');
		$module->components = array();
		foreach ($_->all_components as $cur_component) {
			$conf = configurator_component::factory($cur_component);
			$info = clone $conf->info;
			$info->type = substr($cur_component, 0, 4) == 'tpl_' ? 'template' : 'component';
			$info->disabled = $conf->is_disabled();
			if (!is_null($_->entity_manager->get_entity(
					array('class' => com_packager_package),
					array('&',
						'tag' => array('com_packager', 'package'),
						'strict' => array('name', $cur_component)
					)
				)))
				$info->already_exists = true;
			$module->components[$cur_component] = $info;
		}
	}
}