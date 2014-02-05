<?php
/**
 * com_mysql's configuration defaults.
 *
 * @package Components\mysql
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	array(
		'name' => 'host',
		'cname' => 'Host',
		'description' => 'The default MySQL host.',
		'value' => 'localhost',
	),
	array(
		'name' => 'user',
		'cname' => 'User',
		'description' => 'The default MySQL user.',
		'value' => 'wonder',
	),
	array(
		'name' => 'password',
		'cname' => 'Password',
		'description' => 'The default MySQL password.',
		'value' => 'password',
	),
	array(
		'name' => 'database',
		'cname' => 'Database',
		'description' => 'The default MySQL database.',
		'value' => 'wonder',
	),
	array(
		'name' => 'prefix',
		'cname' => 'Table Prefix',
		'description' => 'The default MySQL table name prefix.',
		'value' => 'wdr_',
	),
);
