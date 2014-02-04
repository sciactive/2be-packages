<?php
/**
 * com_popeye's information.
 *
 * @package Components\popeye
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'jQuery.popeye',
	'author' => 'SciActive (Component), Christoph Schuessler (JavaScript)',
	'version' => '2.1-1.0.1',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'jQuery.popeye jQuery plugin',
	'description' => 'A JavaScript image slideshow jQuery component.',
	'depend' => array(
		'core' => '<3',
		'component' => 'com_jquery'
	),
);