<?php
/**
 * Show configured modules.
 *
 * @package Components\modules
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_modules->show_modules)
	return;

$modules = (array) $_->entity_manager->get_entities(
		array('class' => com_modules_module),
		array('&',
			'tag' => array('com_modules', 'module'),
			'data' => array('enabled', true)
		)
	);
$_->entity_manager->sort($modules, 'order');
foreach ($modules as $cur_module) {
	if ($cur_module->check_conditions())
		$cur_module->print_module();
}
unset($modules, $cur_module);