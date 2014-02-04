<?php
/**
 * com_calendar's modules.
 *
 * @package Components\calendar
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

global $_;
return array(
	'agenda' => array(
		'cname' => 'Agenda',
		'description' => 'Show your agenda.',
		'image' => 'includes/agenda_screen.png',
		'view_callback' => array($_->com_calendar, 'agenda_widget'),
		'form' => 'modules/agenda_form',
		'type' => 'module imodule widget',
		'widget' => array(
			'default' => true,
			'depends' => array(
				'ability' => 'com_calendar/viewcalendar',
			),
		),
	),
);