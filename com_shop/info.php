<?php
/**
 * com_shop's information.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Shops',
	'author' => 'SciActive',
	'version' => '1.1.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'eCommerce shop based on com_sales',
	'description' => 'Sell or display your products from com_sales online through a virtual shop or catalog.',
	'depend' => array(
		'core' => '<3',
		'service' => 'user_manager&icons&uploader',
		'component' => 'com_customer&com_sales&com_jquery&com_bootstrap&com_pgrid&com_pnotify&com_popeye&com_pform'
	),
	'abilities' => array(
		array('manageshops', 'Manage Shops', 'User can manage other users\' shops.'),
		array('listshops', 'List Shops', 'User can see shops.'),
		array('newshop', 'Create Shops', 'User can create new shops.'),
		array('editshop', 'Edit Shops', 'User can edit current shops.'),
		array('deleteshop', 'Delete Shops', 'User can delete current shops.'),
	),
);