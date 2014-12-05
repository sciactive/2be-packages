<?php
/**
 * com_calendar's information.
 *
 * @package Components\calendar
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Calendar',
	'author' => 'SciActive',
	'version' => '1.1.1dev',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'Company Calendar',
	'description' => 'The company calendar can be used to manage employees and customers.',
	'depend' => array(
		'core' => '<3',
		'service' => 'user_manager&editor',
		'component' => 'com_jquery&com_bootstrap&com_pnotify&com_jstree&com_ptags&com_hrm&com_pform'
	),
	'abilities' => array(
		array('viewcalendar', 'View Calendar', 'User can view the calendar.'),
		array('editcalendar', 'Edit Calendar', 'User can edit their personal calendar.'),
		array('managecalendar', 'Manage Calendar', 'User can view private events and manage schedules for other employee.')
	),
);