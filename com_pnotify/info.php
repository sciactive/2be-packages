<?php
/**
 * com_pnotify's information.
 *
 * @package Components\pnotify
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'PNotify',
	'author' => 'SciActive',
	'version' => '1.2.0-1.2.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'PNotify jQuery plugin',
	'description' => 'A JavaScript notification jQuery component. Supports many features, and fully themeable using jQuery UI.',
	'depend' => array(
		'pines' => '<3',
		'service' => 'icons',
		'component' => 'com_jquery&com_bootstrap'
	),
);