<?php
/**
 * Hook URL creation.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_shop->shop_location)
	return;

/**
 * Check for a shop URL.
 *
 * @param array &$array An array of arguments. (The options array and the selectors.)
 * @param mixed $name Unused.
 * @param mixed &$object Unused.
 * @param mixed &$function Unused.
 * @param array &$data The callback data array.
 */
function com_shop__url(&$array, $name, &$object, &$function, &$data) {
	global $_;
	if (!$_->config->com_shop->shop_location)
		return;
	if ($array[0] == 'com_shop') {
		if (!$array[1])
			$array[1] = '';
		if (!$array[2])
			$array[2] = array();
		$array[3] = true;
		$data['com_shop__location'] = $_->config->full_location;
		$_->config->full_location = $_->config->com_shop->shop_location;
	}
}

/**
 * Put the original location back into config.
 *
 * @param array &$array Unused.
 * @param mixed $name Unused.
 * @param mixed &$object Unused.
 * @param mixed &$function Unused.
 * @param array &$data The callback data array.
 */
function com_shop__url_after(&$array, $name, &$object, &$function, &$data) {
	global $_;
	if ($data['com_shop__location'])
		$_->config->full_location = $data['com_shop__location'];
}

$_->hook->add_callback('$_->template->url', -10, 'com_shop__url');
$_->hook->add_callback('$_->template->url', 10, 'com_shop__url_after');