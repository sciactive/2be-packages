<?php
/**
 * tpl_shop's configuration.
 *
 * @package Templates\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

pines_session();

return array(
	array(
		'name' => 'variant',
		'cname' => 'Page Variant/Layout',
		'description' => 'The layout of the page. On two column layouts, the sidebars are combined into one. On full page, the sidebars are not available.',
		'value' => 'full-page',
		'options' => array(
			'threecol (Three columns.)' => 'threecol',
			'twocol-sideleft (Two columns, left sidebar.)' => 'twocol-sideleft',
			'twocol-sideright (Two columns, right sidebar.)' => 'twocol-sideright',
			'full-page (Full page.)' => 'full-page',
		),
		'peruser' => true,
	),
	array(
		'name' => 'width',
		'cname' => 'Width',
		'description' => 'Fluid or fixed width.',
		'value' => 'fixed',
		'options' => array(
			'Fluid Width' => 'fluid',
			'Fixed Width' => 'fixed',
		),
		'peruser' => true,
	),
	array(
		'name' => 'brand_type',
		'cname' => 'Textual Brand Type',
		'description' => 'Choose which textual form to use as the brand in the navbar.',
		'value' => 'System Name',
		'options' => array(
			'System Name',
			'Page Title',
			'Custom',
		),
		'peruser' => true,
	),
	array(
		'name' => 'brand_name',
		'cname' => 'Custom Brand Name',
		'description' => 'Specify a custom brand Name to appear as text.',
		'value' => '',
		'peruser' => true,
	),
	array(
		'name' => 'use_header_image',
		'cname' => 'Use Header Image',
		'description' => 'Show a header image (instead of just text) at the top of the page.',
		'value' => false,
		'peruser' => true,
	),
	array(
		'name' => 'header_image',
		'cname' => 'Header Image',
		'description' => 'The header image to use.',
		'value' => (isset($_SESSION['user']->group) && is_callable(array($_SESSION['user']->group, 'get_logo'))) ? $_SESSION['user']->group->get_logo() : $_->config->location.$_->config->upload_location.'logos/default_logo.png',
		'peruser' => true,
	),
	array(
		'name' => 'alt_navbar',
		'cname' => 'Alternate Navbar',
		'description' => 'Use the Bootstrap theme\'s alternate navbar styling.',
		'value' => false,
		'peruser' => true,
	),
	array(
		'name' => 'fancy_style',
		'cname' => 'Fancy Styling',
		'description' => 'Use fancier styling modifications.',
		'value' => array('printfix', 'printheader'),
		'options' => array(
			'Hide non-content positions.' => 'printfix',
			'Show the page header when non-content positions are hidden.' => 'printheader',
			'No gutters on the sides.' => 'nosidegutters',
		),
		'peruser' => true,
	),
	array(
		'name' => 'ajax',
		'cname' => 'Use Ajax',
		'description' => 'Use the experimental AJAX code to load pages without refreshing.',
		'value' => false,
		'peruser' => true,
	),
);