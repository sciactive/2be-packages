<?php
/**
 * com_su's configuration defaults.
 *
 * @package Components\su
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	array(
		'name' => 'allow_pins',
		'cname' => 'Allow PIN Based Switching',
		'description' => 'Allows users to switch using only the PIN of the target user. (This is not advisable for large installations.)',
		'value' => false,
		'peruser' => true,
	),
);