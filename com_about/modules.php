<?php
/**
 * com_about's modules.
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
	'pinesfeed' => array(
		'cname' => 'WonderPHP News Feed',
		'description' => 'WonderPHP news feed from Twitter.',
		'view' => 'modules/pinesfeed',
		'type' => 'widget',
		'widget' => array(
			'default' => true,
			'depends' => array(
				'ability' => 'com_about/pinesfeed',
			),
		),
	),
);