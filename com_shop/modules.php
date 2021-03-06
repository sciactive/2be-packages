<?php
/**
 * com_shop's modules.
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
	'cart' => array(
		'cname' => 'Shopping Cart',
		'description' => 'View your shopping cart.',
		'view' => 'cart/show',
		'type' => 'module imodule',
	),
	'featured' => array(
		'cname' => 'Featured Item',
		'description' => 'Display featured shop items.',
		'view' => 'modules/featured',
		'form' => 'modules/featured_form',
		'type' => 'module imodule',
	),
);