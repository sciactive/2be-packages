<?php
/**
 * Manage the system groups.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_user/listgroups') )
	punt_user(null, pines_url('com_user', 'listgroups'));

$_->user_manager->list_groups($_REQUEST['enabled'] != 'false');