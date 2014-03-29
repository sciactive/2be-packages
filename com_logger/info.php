<?php
/**
 * com_logger's information.
 *
 * @package Components\logger
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Logger',
	'author' => 'SciActive',
	'version' => '1.0.3',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'services' => array('log_manager'),
	'short_description' => 'System log manager',
	'description' => 'Provides a method for components to log their activity.',
	'depend' => array(
		'core' => '<3',
		'component' => 'com_jquery&com_pgrid&com_jstree&com_pform'
	),
	'abilities' => array(
		array('view', 'View Log', 'Let the user view the 2be log.'),
		array('clear', 'Clear Log', 'Let the user clear (delete) the 2be log.')
	),
);