<?php
/**
 * com_istyle's information.
 *
 * @package Components\istyle
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Inline Module CSS Style',
	'author' => 'SciActive',
	'version' => '1.0.1',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'CSS style inline module',
	'description' => 'An inline module to allow CSS styles. Many editors will filter CSS style elements, so this component can be used to create them.',
	'depend' => array(
		'core' => '<3'
	),
);