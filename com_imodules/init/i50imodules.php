<?php
/**
 * Hook the content formatter.
 *
 * @package Components\imodules
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_imodules->parse_imodules)
	return;

/**
 * Parse imodules in content.
 *
 * @param array &$arguments Arguments.
 */
function com_imodules__parse_imodules(&$arguments) {
	global $_;
	$_->com_imodules->parse_imodules($arguments[0]);
}

$_->hook->add_callback('$_->format_content', -10, 'com_imodules__parse_imodules');