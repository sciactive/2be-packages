<?php
/**
 * Switch the template for mobile users.
 *
 * @package Components\uasniffer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_uasniffer->mobile_site)
	return;

if ($_->depend->check('browser', 'mobile-real') xor ($_COOKIE['com_uasniffer_switch'] == 'true')) {
	$_->config->default_template = $_->config->com_uasniffer->mobile_template;
	$_->config->admin_template = $_->config->com_uasniffer->mobile_template;
}

if ($_->depend->check('browser', 'mobile-real') && $_->config->com_uasniffer->switcher) {
	$module = new module('com_uasniffer', 'switcher', $_->config->com_uasniffer->switcher_pos);
	unset($module);
}