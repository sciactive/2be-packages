<?php
/**
 * Show menu entries.
 *
 * @package Components\menueditor
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

$entries = (array) $_->nymph->getEntities(
		array('class' => com_menueditor_entry),
		array('&',
			'tag' => array('com_menueditor', 'entry'),
			'strict' => array('enabled', true)
		)
	);

$_->nymph->sort($entries, 'sort_order');

foreach ($entries as $cur_entry)
	$_->menu->menu_arrays[] = $cur_entry->menu_array();

unset($entries, $cur_entry);