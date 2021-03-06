<?php
/**
 * com_hrm's modules.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'clockin' => array(
		'cname' => 'Employee Clockin',
		'description' => 'Clock in to your timeclock. Requires "com_hrm/clock" ability.',
		'image' => 'includes/timeclock_widget_screen.png',
		'view' => 'employee/timeclock/clock',
		'type' => 'module imodule widget',
		'widget' => array(
			'default' => true,
			'depends' => array(
				'ability' => 'com_hrm/clock',
			),
		),
	),
);