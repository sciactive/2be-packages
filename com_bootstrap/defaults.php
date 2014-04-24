<?php
/**
 * com_bootstrap's configuration defaults.
 *
 * @package Components\bootstrap
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	array(
		'name' => 'theme',
		'cname' => 'Theme',
		'description' => 'Bootstrap theme. To use your own, put the css file in com_bootstrap/includes/css, and edit the themes.json file.',
		'value' => 'normal',
		'options' => function () {
			$themes = json_decode(file_get_contents('components/com_bootstrap/includes/css/themes.json'), true);
			$return = array();
			foreach ($themes as $key => $theme) {
				$return[$theme['name']] = $key;
			}
			return $return;
		},
		'peruser' => true,
	),
	array(
		'name' => 'grid_columns',
		'cname' => 'Total Grid Columns',
		'description' => 'You must put the total number of columns in your grid layout here. This is used by other components to style their content.',
		'value' => 12,
		'peruser' => true,
	),
	array(
		'name' => 'always_load',
		'cname' => 'Always Load',
		'description' => 'Always load Bootstrap. (Even if no part of the page says it needs it.) HIGHLY RECOMMENDED!',
		'value' => true,
		'peruser' => true,
	),
);