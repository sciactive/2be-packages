<?php
/**
 * Manage the system users.
 *
 * @package XROOM
 * @subpackage com_user
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('D_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_user/manage') ) {
	$config->user_manager->punt_user("You don't have necessary permission.", $config->template->url('com_user', 'manageusers', null, false));
	return;
}

$config->user_manager->list_users();
?>