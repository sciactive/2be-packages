<?php
/**
 * com_packager's configuration defaults.
 *
 * @package Components\packager
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	array(
		'name' => 'global_packages',
		'cname' => 'Globalize Packages',
		'description' => 'Ensure that every user can access all packages by setting the "other" access control to read.',
		'value' => true,
		'peruser' => true,
	),
	array(
		'name' => 'package_path',
		'cname' => 'Package Maker Path',
		'description' => 'The relative path of the directory where packages should be made. End this path with a slash!',
		'value' => $_->config->upload_location.'packages/',
	),
);