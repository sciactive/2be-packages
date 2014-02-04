<?php
/**
 * Add the login menu entry.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_user->login_menu || gatekeeper())
	return;

$_->menu->menu_arrays[] = array(
	'path' => $_->config->com_user->login_menu_path,
	'text' => $_->config->com_user->login_menu_text,
	'href' => array('com_user')
);