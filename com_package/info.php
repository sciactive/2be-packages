<?php
/**
 * com_package's information.
 *
 * @package Components\package
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Package Management Libraries',
	'author' => 'SciActive',
	'version' => '1.0.1',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'WonderPHP package libraries',
	'description' => 'Package management functions. This component is meant to be used by other components.',
	'depend' => array(
		'pines' => '<3',
		'component' => 'com_slim'
	),
);