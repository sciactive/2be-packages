<?php
/**
 * com_shop's mails.
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
	'save_shop' => array(
		'cname' => 'Saved Shop',
		'description' => 'This email is sent when a shop is saved.',
		'view' => 'mails/save_shop',
		'has_recipient' => false,
		'unsubscribe' => true,
		'macros' => array(
			'shop_name' => 'Name of the shop that has been saved.',
		),
	),
);