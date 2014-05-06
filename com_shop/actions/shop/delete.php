<?php
/**
 * Delete a set of shops.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_shop/deleteshop') )
	punt_user(null, pines_url('com_shop', 'shop/list'));

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_shop) {
	$cur_entity = com_shop_shop::factory((int) $cur_shop);
	if ( !isset($cur_entity->guid) || !$cur_entity->delete() )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_shop;
}
if (empty($failed_deletes)) {
	pines_notice('Selected shop(s) deleted successfully.');
} else {
	pines_error('Could not delete shops with given IDs: '.$failed_deletes);
}

pines_redirect(pines_url('com_shop', 'shop/list'));