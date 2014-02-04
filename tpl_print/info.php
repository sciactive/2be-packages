<?php
/**
 * tpl_print's information.
 *
 * @package Templates\print
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Print Template',
	'author' => 'SciActive',
	'version' => '1.0.2',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'services' => array('template'),
	'positions' => array(
		'head',
		'content',
	),
	'short_description' => 'Simple template suitable for printing',
	'description' => 'This template only shows the content modules. It\'s suitable for letting the user print the page without any excess information.',
	'depend' => array(
		'core' => '<3',
		'component' => 'com_jquery&com_bootstrap'
	),
);