<?php
/**
 * com_customertimer's information.
 *
 * @package Pines
 * @subpackage com_customertimer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Customer Timer',
	'author' => 'SciActive',
	'version' => '1.0.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'short_description' => 'Customer account timer',
	'description' => 'Allows the use of com_customer\'s membership and point tracking feature to run a service that requires customers to buy time, such as an internet cafe.',
	'abilities' => array(
		array('listfloors', 'List Floors', 'User can see floors.'),
		array('newfloor', 'Create Floors', 'User can create new floors.'),
		array('editfloor', 'Edit Floors', 'User can edit current floors.'),
		array('timefloor', 'Time Floors', 'User can access floor timers.'),
		array('deletefloor', 'Delete Floors', 'User can delete current floors.'),
		array('login', 'Login Users', 'User can log a customer in to the time tracker.'),
		array('loginpwless', 'Bypass Passwords', 'User can log a customer in without providing its password.'),
		array('logout', 'Logout Users', 'User can log a customer out of the time tracker.')
	),
);

?>