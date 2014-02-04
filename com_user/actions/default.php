<?php
/**
 * Default action of com_user.
 *
 * If the user is not logged in, a login page is provided. If he is, a list of
 * users will be printed.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper() ) {
	$_->user_manager->print_login();
	return;
}

pines_action('com_user', 'listusers');