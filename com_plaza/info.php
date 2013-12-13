<?php
/**
 * com_plaza's information.
 *
 * @package Components\plaza
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'WonderPHP Plaza Package Manager',
	'author' => 'SciActive',
	'version' => '1.0.3',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'WonderPHP Plaza package manager',
	'description' => 'Find, install, and manage packages.',
	'depend' => array(
		'pines' => '<3',
		'component' => 'com_jquery&com_pgrid&com_package&com_pform'
	),
	'recommend' => array(
		'component' => 'com_fancybox'
	),
	'abilities' => array(
		array('listpackages', 'List Packages', 'User can see packages.'),
		array('editpackages', 'Edit Packages', 'User can install/upgrade/remove packages.'),
		array('editrepositories', 'Edit Sources', 'User can change software sources (repositories).')
	),
);

?>