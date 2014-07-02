<?php
/**
 * com_toopaymd's information.
 *
 * @package Components\toopaymd
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Toopay Bootstrap Markdown Editor',
	'author' => 'SciActive',
	'version' => '2.5.0-1.0.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'services' => array('editor'),
	'short_description' => 'Toopay Bootstrap Markdown Editor widget',
	'description' => 'Toopay Bootstrap Markdown Editor based widget.',
	'depend' => array(
		'core' => '<3',
		'component' => 'com_jquery&com_bootstrap&com_pform'
	),
	'recommend' => array(
		'component' => 'com_elfinder'
	),
);