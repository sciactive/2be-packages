<?php
/**
 * com_mailer's information.
 *
 * @package Components\mailer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Mailer',
	'author' => 'SciActive',
	'version' => '1.1.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'Email interface',
	'description' => 'Provides a more object oriented interface for creating emails in WonderPHP. Supports attachments.',
	'depend' => array(
		'core' => '<3',
		'service' => 'entity_manager&editor',
		'component' => 'com_jquery&com_bootstrap&com_pgrid&com_markdown&com_pform'
	),
	'recommend' => array(
		'class' => 'SQLite3'
	),
	'abilities' => array(
		array('listrenditions', 'List Renditions', 'User can see renditions.'),
		array('newrendition', 'Create Renditions', 'User can create new renditions.'),
		array('editrendition', 'Edit Renditions', 'User can edit current renditions.'),
		array('deleterendition', 'Delete Renditions', 'User can delete current renditions.'),
		array('listtemplates', 'List Templates', 'User can see templates.'),
		array('newtemplate', 'Create Templates', 'User can create new templates.'),
		array('edittemplate', 'Edit Templates', 'User can edit current templates.'),
		array('deletetemplate', 'Delete Templates', 'User can delete current templates.'),
	),
);