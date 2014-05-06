<?php
/**
 * Save changes to a shop.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( isset($_REQUEST['id']) ) {
	if ( !gatekeeper('com_shop/editshop') )
		punt_user(null, pines_url('com_shop', 'shop/list'));
	$shop = com_shop_shop::factory((int) $_REQUEST['id']);
	if (!isset($shop->guid)) {
		pines_error('Requested shop id is not accessible.');
		return;
	}
} else {
	if ( !gatekeeper('com_shop/newshop') )
		punt_user(null, pines_url('com_shop', 'shop/list'));
	$shop = com_shop_shop::factory();
}

// General
$shop->name = $_REQUEST['name'];
$shop->enabled = ($_REQUEST['enabled'] == 'ON');
$shop->description = $_REQUEST['description'];
$shop->short_description = $_REQUEST['short_description'];

// Attributes
$shop->attributes = (array) json_decode($_REQUEST['attributes']);
foreach ($shop->attributes as &$cur_attribute) {
	$array = array(
		'name' => $cur_attribute->values[0],
		'value' => $cur_attribute->values[1]
	);
	$cur_attribute = $array;
}
unset($cur_attribute);

if (empty($shop->name)) {
	$shop->print_form();
	pines_notice('Please specify a name.');
	return;
}
$test = $_->entity_manager->get_entity(array('class' => com_shop_shop, 'skip_ac' => true), array('&', 'tag' => array('com_shop', 'shop'), 'data' => array('name', $shop->name)));
if (isset($test) && $test->guid != $_REQUEST['id']) {
	$shop->print_form();
	pines_notice('There is already a shop with that name. Please choose a different name.');
	return;
}

if ($_->config->com_shop->global_shops)
	$shop->ac->other = 1;

if ($shop->save()) {
	pines_notice('Saved shop ['.$shop->name.']');
	$_->com_mailer->send_mail('com_shop/save_shop', array('shop_name' => h($shop->name)));
} else {
	pines_error('Error saving shop. Do you have permission?');
}

pines_redirect(pines_url('com_shop', 'shop/list'));