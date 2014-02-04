<?php
/**
 * Log a user into the system.
 *
 * @package Components\timeoutnotice
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

$_->page->override = true;
header('Content-Type: application/json');

if ( empty($_REQUEST['username']) ) {
	$_->page->override_doc('false');
	return;
}
if ( gatekeeper() && $_REQUEST['username'] == $_SESSION['user']->username ) {
	$_->page->override_doc('true');
	return;
}

$user = user::factory($_REQUEST['username']);
if (!$user->check_password($_REQUEST['password']))
	unset($user);

if ( isset($user, $user->guid) && $_->user_manager->login($user) ) {
	$_->page->override_doc('true');
} else {
	$_->page->override_doc('false');
}
