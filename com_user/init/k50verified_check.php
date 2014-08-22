<?php
/**
 * Check to see if the user is verified, allowing them to resend the link.
 * 
 * If their account is not verified, they can request that the verification link
 * be resent to their email address.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_user->verify_email)
	return;

// Check to see if they're not verified.
if (gatekeeper() && isset($_SESSION['user']->secret) && !$_->depend->check('request', 'com_user/registeruser')) {
	// Provide a notice that they're not verified.
	$module = new module('com_user', 'resend_verification', 'bottom');
	unset($module);
}