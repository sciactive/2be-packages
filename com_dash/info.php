<?php
/**
 * com_dash's information.
 *
 * @package Components\dash
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Dashboard',
	'author' => 'SciActive',
	'version' => '1.0.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'Configurable dashboard',
	'description' => 'Provides a dashboard with quick links and widgets.',
	'depend' => array(
		'core' => '>=1.0.4&<3',
		'service' => 'user_manager',
		'component' => 'com_jquery&com_bootstrap&com_pform'
	),
	'abilities' => array(
		array('dash', 'Dashboard', 'User can have a dashboard.'),
		array('editdash', 'Edit Dashboard', 'User can edit their dashboard.'),
		array('manage', 'Manage Dashboards', 'User can manage all dashboards. Including default dashboards.')
	),
);