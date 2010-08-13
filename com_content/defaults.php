<?php
/**
 * com_content's configuration defaults.
 *
 * @package Pines
 * @subpackage com_content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

return array(
	array(
		'name' => 'banned_tags',
		'cname' => 'Banned Tags',
		'description' => 'These tags will not be allowed in pages.',
		'value' => array('script', 'style'),
		'peruser' => true,
	),
	array(
		'name' => 'ac_page_group',
		'cname' => 'Page Group Access',
		'description' => 'The level of access the user\'s primary group has to their pages. This will be applied when saving pages.',
		'value' => 2,
		'options' => array(
			'None' => 0,
			'Read Only' => 1,
			'Read/Write' => 2,
			'Read/Write/Delete' => 3
		),
		'peruser' => true,
	),
	array(
		'name' => 'ac_page_other',
		'cname' => 'Page Other Access',
		'description' => 'The level of access other users have to pages. This will be applied when saving pages.',
		'value' => 0,
		'options' => array(
			'None' => 0,
			'Read Only' => 1,
			'Read/Write' => 2,
			'Read/Write/Delete' => 3
		),
		'peruser' => true,
	),
);

?>