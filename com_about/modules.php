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
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'newsfeed' => array(
		'cname' => '2be News Feed',
		'description' => '2be.io news feed from Twitter.',
		'view' => 'modules/newsfeed',
		'type' => 'widget',
		'widget' => array(
			'default' => true,
			'depends' => array(
				'ability' => 'com_about/newsfeed',
			),
		),
	),
);