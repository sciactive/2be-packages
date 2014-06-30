<?php
/**
 * tpl_shop's information.
 *
 * @package Templates\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Shop Template',
	'author' => 'SciActive',
	'version' => '1.0.0alpha1',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'services' => array('template'),
	'positions' => array(
		'shop_thumbnail',
		'head',
		'top',
		'header',
		'header_right',
		'breadcrumbs',
		'pre_content',
		'left',
		'content_top_left',
		'content_top_right',
		'content',
		'content_bottom_left',
		'content_bottom_right',
		'right',
		'post_content',
		'footer',
		'bottom',
	),
	'short_description' => 'Shop template',
	'description' => 'A template with shop integration, completely styled with Twitter Bootstrap.',
	'depend' => array(
		'core' => '<3',
		'component' => 'com_jquery&com_bootstrap'
	),
	'recommend' => array(
		'component' => 'com_pnotify'
	),
);
