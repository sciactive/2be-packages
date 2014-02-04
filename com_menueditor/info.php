<?php
/**
 * com_menueditor's information.
 *
 * @package Components\menueditor
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Menu Editor and Provider',
	'author' => 'SciActive',
	'version' => '1.1.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'Edit menus and associate menu entries with various items',
	'description' => 'A menu editor that allows you to edit menu entries. It also lets other components provide user managed menu entries for any of their items.',
	'depend' => array(
		'pines' => '<3',
		'service' => 'entity_manager&icons',
		'component' => 'com_jquery&com_bootstrap&com_pgrid&com_markdown&com_pform&com_jstree'
	),
	'abilities' => array(
		array('listentries', 'List Entries', 'User can see menu entries.'),
		array('newentry', 'Create Entries', 'User can create new entries.'),
		array('editentry', 'Edit Entries', 'User can edit current entries.'),
		array('deleteentry', 'Delete Entries', 'User can delete current entries.'),
		array('jsentry', 'Edit Entry JavaScript', 'User can edit the onclick JavaScript value of entries.')
	),
);