<?php
/**
 * Load the steps module on the login page during checkout.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (gatekeeper())
	return;

/**
 * Check for the login page to attach the steps module.
 *
 * @param array &$array An array of arguments.
 */
function com_shop__catch_login_page(&$array) {
	if ((!isset($array[0]) || $array[0] == 'content')) {
		$url = pines_url('com_shop', 'checkout/shipping');
		if ($array[1] == $url || $_REQUEST['url'] == $url) {
			global $_;
			// Load the steps module.
			$_->com_shop->checkout_step('1');
		}
	}
}

$_->hook->add_callback('$_->user_manager->print_login', -10, 'com_shop__catch_login_page');