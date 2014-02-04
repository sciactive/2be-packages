<?php
/**
 * Hook the content formatter.
 *
 * @package Components\replace
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_replace->search_replace)
	return;

/**
 * Search and replace in content.
 *
 * @param array &$arguments Arguments.
 */
function com_replace__search_replace(&$arguments) {
	global $_;
	$_->com_replace->search_replace($arguments[0]);
}

$_->hook->add_callback('$_->format_content', -10, 'com_replace__search_replace');