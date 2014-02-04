<?php
/**
 * List entries.
 *
 * @package Components\menueditor
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_menueditor/listentries') )
	punt_user(null, pines_url('com_menueditor', 'entry/list'));

$_->com_menueditor->list_entries();