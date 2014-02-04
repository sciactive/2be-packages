<?php
/**
 * com_customertimer's configuration defaults.
 *
 * @package Components\customertimer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	array(
		'name' => 'debt_login',
		'cname' => 'Debt Login',
		'description' => 'Allow customers to log in when their account is overdrawn.',
		'value' => false,
		'peruser' => true,
	),
	array(
		'name' => 'ppm',
		'cname' => 'Points per Minute',
		'description' => 'The amount of points to subtract from the user\'s account for every minute they are logged in.',
		'value' => 1,
		'peruser' => true,
	),
	array(
		'name' => 'level_warning',
		'cname' => 'Warning Level',
		'description' => 'Amount of points to consider the customer to be in the warning level.',
		'value' => 10,
		'peruser' => true,
	),
	array(
		'name' => 'level_critical',
		'cname' => 'Critical Level',
		'description' => 'Amount of points to consider the customer to be in the critical level.',
		'value' => 3,
		'peruser' => true,
	),
	array(
		'name' => 'global_floors',
		'cname' => 'Globalize Floors',
		'description' => 'Ensure that every user can access all floors by setting the "other" access control to read.',
		'value' => true,
		'peruser' => true,
	),
);