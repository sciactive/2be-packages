<?php
/**
 * Check Entity classes for a helper.
 *
 * @package Components\entityhelper
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('system/all') )
	punt_user(null, pines_url('com_entityhelper', 'classcheck'));

$entity_classes = array();
// Look at each class.
foreach ($_->class_files as $cur_class_file) {
	$cur_class = basename($cur_class_file, '.php');
	if ($cur_class != 'Entity' && is_subclass_of($cur_class, 'Entity'))
		$entity_classes[] = $cur_class;
}

$no_helper = array();
// Find the ones without a custom helper.
foreach ($entity_classes as $cur_class) {
	if (!method_exists($cur_class, 'helper'))
		$no_helper[] = $cur_class;
}

$module = new module('com_entityhelper', 'classcheck', 'content');
$module->entity_classes = $entity_classes;
$module->no_helper = $no_helper;