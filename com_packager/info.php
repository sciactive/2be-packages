<?php
/**
 * com_packager's information.
 *
 * @package Components\packager
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Package Creator',
	'author' => 'SciActive',
	'version' => '1.1.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => '2be package creator',
	'description' => 'Package your components and templates into a 2be repository ready Slim archive. You can use these packages to distribute your component to other 2be users.',
	'depend' => array(
		'core' => '<3',
		'service' => 'uploader&editor&configurator',
		'component' => 'com_slim&com_jquery&com_bootstrap&com_pgrid&com_ptags&com_pform',
		'package' => 'com_packager-data'
	),
	'abilities' => array(
		array('listpackages', 'List Packages', 'User can see packages.'),
		array('newpackage', 'Create Packages', 'User can create new packages.'),
		array('editpackage', 'Edit Packages', 'User can edit current packages.'),
		array('makepackage', 'Make Packages', 'User can make packages.'),
		array('deletepackage', 'Delete Packages', 'User can delete current packages.')
	),
);