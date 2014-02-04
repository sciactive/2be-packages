<?php
/**
 * com_about's information.
 *
 * @package Components\about
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'About',
	'author' => 'SciActive',
	'version' => '1.1.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'Configurable about page',
	'description' => 'Displays configurable information about WonderPHP and your installation.',
	'depend' => array(
		'pines' => '<3'
	),
	'recommend' => array(
		'component' => 'com_bootstrap'
	),
	'abilities' => array(
		array('show', 'About Page', 'User can see the about page.'),
		array('pinesfeed', 'WonderPHP Feed', 'User can see the WonderPHP News Feed widget.')
	),
);