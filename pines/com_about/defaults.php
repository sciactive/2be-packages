<?php
/**
 * com_about's configuration defaults.
 *
 * @package Pines
 * @subpackage com_about
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

return array(
	array(
		'name' => 'description',
		'cname' => 'Description',
		'description' => 'Description of your installation.',
		'value' => 'This is the default installation of Pines.',
	),
	array(
		'name' => 'describe_self',
		'cname' => 'Describe Pines',
		'description' => 'Whether to show Pines\' description underneath yours.',
		'value' => true,
	),
);

?>