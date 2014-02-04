<?php
/**
 * com_pgrid's information.
 *
 * @package Components\pgrid
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'PGrid',
	'author' => 'SciActive',
	'version' => '1.0.1',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'PGrid jQuery plugin',
	'description' => 'A JavaScript data grid jQuery component. Supports many features, and fully themeable using jQuery UI.',
	'depend' => array(
		'pines' => '<3',
		'service' => 'icons',
		'component' => 'com_jquery'
	),
	'abilities' => array(
		array('clearallstates', 'Clear All States', 'Clear all users\' pgrid states.')
	),
);