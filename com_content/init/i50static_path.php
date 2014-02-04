<?php
/**
 * Replace paths.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ($_->config->static_location === '' || !$_->config->com_content->replace_static)
	return;

/**
 * Replace paths to the upload dir with static paths.
 *
 * @param array &$array Return value array.
 */
function com_content__replace_static(&$array) {
	global $_;
	$array[0] = str_replace($_->config->rela_location.$_->config->upload_location, $_->config->static_location.$_->config->upload_location, $array[0]);
}

$_->hook->add_callback('$_->format_content', 10, 'com_content__replace_static');