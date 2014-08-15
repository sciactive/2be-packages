<?php
/**
 * tpl_mobile's configuration.
 *
 * @package Templates\mobile
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	array(
		'name' => 'use_header_image',
		'cname' => 'Use Header Image',
		'description' => 'Show a header image (instead of just text) at the top of the page.',
		'value' => true,
		'peruser' => true,
	),
	array(
		'name' => 'header_image',
		'cname' => 'Header Image',
		'description' => 'The header image to use. Should be 40 pixels tall.',
		'value' => $_->config->rela_location.'templates/tpl_mobile/images/default_logo.png',
		'peruser' => true,
	),
);