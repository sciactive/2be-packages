<?php
/**
 * com_pdf's configuration defaults.
 *
 * @package Components\pdf
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	array(
		'name' => 'author',
		'cname' => 'Default Author',
		'description' => 'The default author of PDFs created by com_pdf.',
		'value' => '2be',
		'peruser' => true,
	),
	array(
		'name' => 'pdf_path',
		'cname' => 'PDF Library Path',
		'description' => 'The relative path of the directory containing the PDFs. End this path with a slash!',
		'value' => $_->config->upload_location.'pdf/',
	),
);