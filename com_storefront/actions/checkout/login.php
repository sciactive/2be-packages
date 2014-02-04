<?php
/**
 * Begin checkout.
 *
 * @package Components\storefront
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ($_->config->com_storefront->catalog_mode)
	return;

if (!gatekeeper()) {
	// Not logged in already.
	// Print a login form.
	$_->user_manager->print_login('content', pines_url('com_storefront', 'checkout/shipping'));
} else {
	// Logged in.
	pines_redirect(pines_url('com_storefront', 'checkout/shipping'));
}