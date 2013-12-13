<?php
/**
 * com_replace's configuration defaults.
 *
 * @package Components\replace
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	array(
		'name' => 'search_replace',
		'cname' => 'Search and Replace',
		'description' => 'Perform the search and replace feature.',
		'value' => true,
		'peruser' => true,
	),
);